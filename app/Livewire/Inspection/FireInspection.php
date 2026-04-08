<?php

namespace App\Livewire\Inspection;

use App\Models\User;
use Livewire\Component;
use App\Models\Location;
use App\Helpers\FileHelper;
use Livewire\WithFileUploads;
use App\Models\FireProtection;
use App\Models\EquipmentMaster;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Models\InspectionChecklist;

class FireInspection extends Component
{
    use WithFileUploads;

    public $type = 'Fire Extinguisher'; // Default
    public $location, $inspection_date, $inspected_by, $remarks, $area;

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048')]
    public $dokumentasi = [];
    public $dokumentasi_paths = [];

    public $searchResponsibility = '';
    public $pelapors = [];
    public $selected_location_specific = [];
    public $showPelaporDropdown = false;
    public $manualPelaporMode = false;
    public $manualPelaporName = '';
    public $responsible_id, $equipment_master_id;
    public $inspected_users = [];
    public $fields = [];

    // Untuk fitur pencarian lokasi
    public $location_id;
    public $show_location = false;
    public $locations = [];
    public $searchLocation = '';
    // End fitur pencarian lokasi spesifik
    public $selected_location;



    // Tempat menyimpan hasil checklist dan technical data
    public $conditions = [];
    // --- PROPERTI FOTO AREA ---
    #[Validate('required|image|max:3072')] // Maksimal 3MB
    public $foto_area, $foto_area_path;
    // Definisi kriteria (Master Fields)


    public function mount()
    {
        $this->inspection_date = now()->format('Y-m-d');
        $this->updatedType($this->type);
    }


    // --- LOGIKA HAPUS FOTO AREA (SEBELUM SAVE) ---
    public function removeFotoArea()
    {
        $this->foto_area = null;
    }
    public function rules()
    {
        return [

            'inspection_date' => 'required|date',
            'inspected_users' => 'required|array|min:1',
            'type'            => 'required|string',
            'location_id'     => 'required',
            'conditions'      => 'required|array',
            'remarks'         => 'required|string|min:5',
        ];
    }

    protected function messages()
    {
        return [

            'inspection_date.required' => 'Tanggal inspeksi wajib diisi.',
            'inspected_users.required' => 'Minimal satu orang pemeriksa wajib dipilih.',
            'type.required'            => 'Jenis alat wajib dipilih.',
            'location_id.required'     => 'Area wajib diisi.',
            'remarks.required'         => 'Catatan/Remarks wajib diisi.',
            'foto_area.required'      => 'Foto area inspeksi wajib diunggah.',
        ];
    }

    /**
     * LOGIC PENCARIAN AREA (LOCATION)
     */
    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->show_location = true;
        } else {
            $this->locations = [];
            $this->show_location = false;
        }
        $this->reset(['location_id']);
    }
    public function getChecklistFromDB()
    {
        $master = DB::table('inspection_checklists')
            ->where('equipment_type', $this->type)
            ->where(function ($q) {
                // 1. Cek berdasarkan location_keyword (Area Umum seperti Maesa Camp)
                $q->where('location_keyword', 'Default')
                    ->orWhereRaw('? LIKE CONCAT("%", location_keyword, "%")', [$this->searchLocation]);
            })
            ->where(function ($q) {
                // 2. Cek berdasarkan kolom baru: location_specific (Nama Alat Spesifik)
                $q->where('location_specific', 'Default')
                    ->when($this->selected_location, function ($subQ) {
                        // Mencocokkan jika selected_location (misal: SCBA Drager) ada di kolom location_specific
                        $subQ->orWhere('location_specific', $this->selected_location);
                    });
            })
            /**
             * Prioritas Pengurutan:
             * Kolom location_specific yang bukan 'Default' akan naik ke atas (Prioritas 1)
             * Kolom location_keyword yang bukan 'Default' (Prioritas 2)
             * Sisanya Default (Prioritas terakhir)
             */
            ->orderByRaw("CASE WHEN location_specific != 'Default' THEN 1
                           WHEN location_keyword != 'Default' THEN 2
                           ELSE 3 END")
            ->first();

        if ($master) {
            $this->fields[$this->type] = [
                'inputs' => json_decode($master->inputs, true),
                'checks' => json_decode($master->checks, true),
            ];
        }
    }
    public function selectLocation($id, $name)
    {
        $this->location_id = $id;
        $this->searchLocation = $name;
        $this->area = $name;
        $this->show_location = false;

        $this->getChecklistFromDB();

        $allMaster = EquipmentMaster::where('location_id', $id)
            ->where('type', $this->type)
            ->get();

        $this->conditions = [];

        foreach ($allMaster as $master) {
            $this->conditions[$master->id] = [];

            // 1. Isi technical data (Read-only fields)
            if ($master->technical_data) {
                foreach ($master->technical_data as $key => $val) {
                    $this->conditions[$master->id][$key] = $val;
                }
            }

            // 2. Inisialisasi Checklist (Logika Baru)
            if (isset($this->fields[$this->type]['checks'])) {
                foreach ($this->fields[$this->type]['checks'] as $checkField) {

                    // Ekstrak nama dan tipe (Handling data array maupun string lama)
                    $name = is_array($checkField) ? $checkField['name'] : $checkField;
                    $type = is_array($checkField) ? ($checkField['type'] ?? 'checkbox') : 'checkbox';

                    if ($type === 'checkbox') {
                        // Default Aman (True)
                        $this->conditions[$master->id][$name] = true;
                    } else {
                        // Default Kosong untuk input text (seperti BAR/PSI)
                        $this->conditions[$master->id][$name] = '';
                    }
                }
            }

            // 3. Inisialisasi Remarks per alat
            // Catatan: Gunakan array di dalam conditions atau property terpisah
            // Jika Anda ingin menyimpan per baris, pastikan di blade memanggil remarks[$master->id]
            $this->remarks[$master->id] = '';
        }
    }



    /**
     * LOGIC PILIH ALAT (SPECIFIC LOCATION)
     */
    public function updatedLocation($value)
    {
        if (!$value) return;
        $master = EquipmentMaster::find($value);

        if ($master) {
            $this->equipment_master_id = $master->id;

            // 1. Reset conditions dulu
            $this->conditions = [];

            // 2. Isi technical data dari database ke conditions (Readonly di UI)
            if ($master->technical_data) {
                foreach ($master->technical_data as $key => $val) {
                    $this->conditions[$key] = $val;
                }
            }
            // 3. Inisialisasi Checklist (Default: TRUE / Aman)
            // 3. Inisialisasi Checklist
            if (isset($this->fields[$this->type]['checks'])) {
                foreach ($this->fields[$this->type]['checks'] as $checkField) {

                    // Cek apakah $checkField adalah array (format baru) atau string (format lama)
                    $name = is_array($checkField) ? $checkField['name'] : $checkField;
                    $type = is_array($checkField) ? $checkField['type'] : 'checkbox';

                    // Logika inisialisasi nilai default
                    if ($type === 'checkbox') {
                        // Jika checkbox, defaultnya TRUE (Aman)
                        $this->conditions[$master->id][$name] = true;
                    } else {
                        // Jika text/input (seperti BAR/PSI), defaultnya string kosong
                        $this->conditions[$master->id][$name] = '';
                    }
                }
            }
        }
    }
    public function updatedType($value)
    {
        $this->reset(['location', 'equipment_master_id', 'conditions', 'selected_location_specific']);
        // Logika Khusus untuk Maesa Camp
        $this->getChecklistFromDB();
        // Jika area sudah terpilih, refresh daftar alat di area tersebut
        if ($this->location_id) {
            $this->selectLocation($this->location_id, $this->searchLocation);
        }
    }

    /**
     * LOGIC PELAPOR / INSPECTED BY
     */
    public function updatedSearchResponsibility()
    {
        if (strlen($this->searchResponsibility) > 1) {
            $this->pelapors = User::where('name', 'like', '%' . $this->searchResponsibility . '%')
                ->limit(10)
                ->get();
            $this->showPelaporDropdown = true;
        } else {
            $this->showPelaporDropdown = false;
        }
    }

    public function selectPelapor($id, $name)
    {
        if (!collect($this->inspected_users)->contains('id', $id)) {
            $this->inspected_users[] = ['id' => $id, 'name' => $name];
        }
        $this->reset(['searchResponsibility', 'showPelaporDropdown']);
    }

    public function enableManualPelapor()
    {
        if ($this->searchResponsibility) {
            $this->inspected_users[] = ['id' => null, 'name' => $this->searchResponsibility];
            $this->reset(['searchResponsibility', 'showPelaporDropdown']);
        }
    }

    public function removeInspectedUser($index)
    {
        unset($this->inspected_users[$index]);
        $this->inspected_users = array_values($this->inspected_users);
    }

    /**
     * SIMPAN DATA
     */
    public function removeImage($id)
    {
        // Cara 1: Jika menggunakan array
        if (isset($this->dokumentasi[$id])) {
            unset($this->dokumentasi[$id]);
        }

        // Cara 2: Jika ingin memastikan tampilan terupdate dengan menyetel null
        // $this->dokumentasi[$id] = null;
    }
    public function getInspectionNumberProperty()
    {
        // 1. Singkatan Area (Contoh: Tokatindung Site -> TS)
        $acronym = collect(explode(' ', $this->area))
            ->map(fn($w) => mb_substr($w, 0, 1))
            ->implode('');

        // 2. Kode Alat (Ambil 3-4 huruf pertama saja, misal: Hydrant -> HYD)
        $equipment = strtoupper(substr($this->type, 0, 3));

        // 3. Format Tanggal Ringkas (Contoh: Fri/060226)
        // D = Nama hari 3 huruf (Mon, Tue, Wed...)
        // d = Tanggal (06)
        // m = Bulan (02)
        // y = Tahun 2 digit (26)
        $dateParsed = \Carbon\Carbon::parse($this->inspection_date);
        $formattedDate = $dateParsed->format('d/m/y');

        return "{$acronym}/{$equipment}/{$formattedDate}";
    }
    // Hook untuk Foto Area
    public function updatedFotoArea()
    {
        $this->validate(['foto_area' => 'image|max:10240']);

        if ($this->foto_area_path) {
            FileHelper::deleteFile($this->foto_area_path);
        }

        $result = FileHelper::compressAndStore($this->foto_area, 'inspections/area-photos');

        $this->foto_area_path = $result;
    }
    public function updatedDokumentasi($value, $key)
    {
        // $key di sini adalah $equipmentMasterId (contoh: dokumentasi.5)
        // $value adalah objek file yang baru diupload

        // 1. Validasi file
        $this->validate([
            'dokumentasi.' . $key => 'image|max:10240', // Max 10MB
        ]);

        // 2. Hapus file lama jika user mengganti gambar untuk ID yang sama
        if (isset($this->dokumentasi_paths[$key])) {
            FileHelper::deleteFile($this->dokumentasi_paths[$key]);
        }

        // 3. Jalankan Kompresi
        // Karena $value adalah objek file, kita langsung kirim ke helper
        $result = FileHelper::compressAndStore($value, 'inspections/documents');

        // 4. Simpan path hasil kompresi ke array paths
        $this->dokumentasi_paths[$key] = $result;
    }
    public function save()
    {
        $this->validate([
            'inspection_date' => 'required|date',
            'location_id'     => 'required',
            'inspected_users' => 'required|array|min:1',
            'conditions'      => 'required|array|min:1',
            'foto_area'       => 'required|image|max:3072',
            'dokumentasi.*'   => 'nullable|image|max:2048',
        ]);

        try {
            $inspectedByString = implode('|', array_column($this->inspected_users, 'name'));
            $generatedNumber = $this->inspection_number;

            DB::transaction(function () use ($inspectedByString, $generatedNumber) {

                // 1. SIMPAN KE HEADER (inspection_sessions)
                $areaPhotoPath = $this->foto_area ? $this->foto_area_path : null;

                $sessionId = DB::table('inspection_sessions')->insertGetId([
                    'inspection_date'   => $this->inspection_date,
                    'inspection_number' => $generatedNumber,
                    'area_name'         => $this->area,
                    'area_photo_path'   => $areaPhotoPath,
                    'submitted_by'      => auth()->user()->name ?? 'System',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // 2. SIMPAN KE DETAIL (fire_protections)
                foreach ($this->conditions as $equipmentMasterId => $dataKondisi) {

                    $documentationPath = $this->dokumentasi_paths[$equipmentMasterId] ?? null;

                    // Ambil Remarks dari property $this->remarks (karena di selectLocation kita inisialisasi di sana)
                    $rowRemarks = $this->remarks[$equipmentMasterId] ?? null;

                    // --- PROSES PEMBERSIHAN DATA ---
                    // Kita perlu membuang field technical_data (seperti FE No, Capacity) dari JSON conditions
                    $masterObj = EquipmentMaster::find($equipmentMasterId);

                    // Ambil semua keys yang merupakan data teknis statis
                    $techKeys = $masterObj && $masterObj->technical_data
                        ? array_keys($masterObj->technical_data)
                        : [];

                    // Filter: Hanya simpan data yang merupakan hasil checklist/inspeksi
                    $cleanConditions = collect($dataKondisi)
                        ->forget(['remarks']) // Pastikan remarks tidak masuk ke JSON conditions
                        ->forget($techKeys)   // Buang data teknis (FE No, Kapasitas, dll) agar JSON tetap bersih
                        ->toArray();

                    FireProtection::create([
                        'inspection_session_id' => $sessionId,
                        'equipment_master_id'   => $equipmentMasterId,
                        'documentation_path'    => $documentationPath,
                        'inspected_by'          => $inspectedByString,
                        'conditions'            => $cleanConditions,
                        'remarks'               => $rowRemarks,
                    ]);
                }
            });

            $this->reset(['dokumentasi', 'conditions', 'foto_area', 'remarks', 'inspected_users']);
            $this->updatedType($this->type); // Refresh form untuk input baru

            $this->dispatch('alert', [
                'text' => "Data inspeksi berhasil disimpan per sesi!",
                'backgroundColor' => "background: #00c853;"
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'text' => "Kesalahan: " . $e->getMessage(),
                'backgroundColor' => "background: #f44336;"
            ]);
        }
    }

    public function resetForm()
    {
        $this->reset(['location', 'remarks', 'dokumentasi', 'inspected_users', 'equipment_master_id', 'conditions', 'foto_area']);
        $this->inspection_date = now()->format('Y-m-d');
        $this->updatedType($this->type);
    }

    public function render()
    {
        $allMasterData = EquipmentMaster::where('location_id', $this->location_id)
            ->where('type', $this->type)
            ->spesificLocation($this->selected_location)
            ->get();
        return view('livewire.inspection.fire-inspection', [
            'allMasterData' => $allMasterData,
            'equipmentMasters' => EquipmentMaster::search($this->type)
                ->byArea($this->searchLocation)
                ->orderBy('specific_location')
                ->limit(10)
                ->get(),
            'availableTypes' => InspectionChecklist::distinct()->pluck('equipment_type'),
        ]);
    }
}
