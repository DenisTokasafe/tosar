<?php

namespace App\Livewire\Inspection;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Livewire\Component;
use App\Models\Location;
use App\Helpers\FileHelper;
use Livewire\WithFileUploads;
use App\Models\FireProtection;
use App\Models\EquipmentMaster;
use Livewire\Attributes\Validate;

class FireInspection extends Component
{
    use WithFileUploads;

    public $type = 'Fire Extinguisher'; // Default
    public $location, $inspection_date, $inspected_by, $remarks, $area;

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048')]
    public $dokumentasi = [];

    public $searchResponsibility = '';
    public $pelapors = [];
    public $selected_location_specific = [];
    public $showPelaporDropdown = false;
    public $manualPelaporMode = false;
    public $manualPelaporName = '';
    public $responsible_id, $equipment_master_id;
    public $inspected_users = [];


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
    public $foto_area;
    // Definisi kriteria (Master Fields)
    public $fields = [
        'Fire Extinguisher' => [
            'checks' => ['Nozzle', 'Hose', 'Pressure Indicator', 'Head Cap', 'Pin', 'Hook', 'Usage Guide', 'FE Sign']
        ],
        'Fire Hose Cabinet' => [
            'checks' => ['Hose', 'Rack', 'Nozzle', 'Valve']
        ],
        'Muster Point' => [
            'checks' => ['Access', 'Visibility', 'Colour', 'Condition of Board', 'Condition of Pole', 'Letter'],
        ],
        'Fire Hydrant' => [
            'checks' => ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'],
        ],
        'Eyewash & Safety Shower' => [
            'checks' => ['Water', 'Caps', 'Nozzle', 'Handle', 'Access', 'Safety Light', 'Cleanliness'],
        ],
        'Fire Hose Reel' => [
            'checks' => ['Hose', 'Reel', 'Nozzle', 'Valve', 'Air', 'Cover'],
        ],
        'Fire sprinkler system' => [
            'checks' => ['Line Pipa', 'Main Valve', 'Drain Valve', 'Test valve', 'Alarm', 'Pressure', 'Access'],
        ],
        'Ring Buoy' => [
            'checks' => ['Ring Buoy', 'Access', 'Tempat Ring Buoy', 'Tali'],
        ],
    ];

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
                $q->where('location_keyword', 'Default')
                    ->orWhereRaw('? LIKE CONCAT("%", location_keyword, "%")', [$this->searchLocation]);
            })
            // Mengambil yang lokasi spesifik (seperti Maesa Camp) dulu, baru Default
            ->orderByRaw("CASE WHEN location_keyword = 'Default' THEN 2 ELSE 1 END")
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

        // Ambil checklist dinamis dari DB (Menggantikan if-else manual)
        $this->getChecklistFromDB();

        // Ambil semua alat
        // 1. Ambil semua data master berdasarkan lokasi dan tipe
        $allMaster = EquipmentMaster::where('location_id', $id)
            ->where('type', $this->type)
            ->get();

        $this->conditions = []; // Reset

        // 2. Looping setiap alat yang ditemukan
        foreach ($allMaster as $master) {
            // Inisialisasi array untuk ID master ini
            $this->conditions[$master->id] = [];

            // Isi technical data (misal: FE No, Capacity) ke dalam conditions
            if ($master->technical_data) {
                foreach ($master->technical_data as $key => $val) {
                    $this->conditions[$master->id][$key] = $val;
                }
            }

            // 3. Inisialisasi Checklist (Default: TRUE / Aman)
            if (isset($this->fields[$this->type]['checks'])) {
                foreach ($this->fields[$this->type]['checks'] as $checkField) {
                    // Data checklist disimpan berdasarkan ID Master dan Nama Check-nya
                    $this->conditions[$master->id][$checkField] = true;
                }
            }

            // Inisialisasi Remarks kosong untuk tiap alat
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
            if (isset($this->fields[$this->type]['checks'])) {
                foreach ($this->fields[$this->type]['checks'] as $checkField) {
                    $this->conditions[$checkField] = true;
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
    public function save()
    {
        $this->validate([
            'inspection_date' => 'required|date',
            'location_id'     => 'required',
            'inspected_users' => 'required|array|min:1',
            'conditions'      => 'required|array|min:1',
            'foto_area'       => 'required|image|max:3072', // Foto Area jadi wajib di header
            'dokumentasi.*'   => 'nullable|image|max:2048',
        ]);

        try {
            $inspectedByString = implode('|', array_column($this->inspected_users, 'name'));
            $generatedNumber = $this->inspection_number;

            DB::transaction(function () use ($inspectedByString, $generatedNumber) {

                // 1. SIMPAN KE HEADER (inspection_sessions)
                $areaPhotoPath = null;
                if ($this->foto_area) {
                    $areaPhotoPath = FileHelper::compressAndStore(
                        $this->foto_area,
                        'inspections/area-photos'
                    );
                }

                // Buat record session sebagai induk
                $sessionId = DB::table('inspection_sessions')->insertGetId([
                    'inspection_date' => $this->inspection_date,
                    'inspection_number'   => $generatedNumber,
                    'area_name'       => $this->area, // Diambil dari selectLocation
                    'area_photo_path' => $areaPhotoPath,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // 2. SIMPAN KE DETAIL (fire_protections)
                foreach ($this->conditions as $equipmentMasterId => $dataKondisi) {

                    $documentationPath = null;
                    if (isset($this->dokumentasi[$equipmentMasterId])) {
                        $documentationPath = FileHelper::compressAndStore(
                            $this->dokumentasi[$equipmentMasterId],
                            'inspections/documents'
                        );
                    }

                    $rowRemarks = $dataKondisi['remarks'] ?? null;
                    // Hilangkan key yang bukan checklist sebelum simpan ke JSON
                    $cleanConditions = collect($dataKondisi)->forget(['remarks'])->toArray();

                    FireProtection::create([
                        'inspection_session_id' => $sessionId, // <--- RELASI BARU
                        'equipment_master_id' => $equipmentMasterId,
                        'documentation_path'  => $documentationPath,
                        'inspected_by'    => $inspectedByString,
                        'submitted_by'        => auth()->user()->name ?? 'System',
                        'conditions'          => $cleanConditions,
                        'remarks'             => $rowRemarks,
                    ]);
                }
            });

            $this->reset(['dokumentasi', 'conditions', 'foto_area']);
            $this->dispatch('alert', ['text' => "Data inspeksi berhasil disimpan per sesi!", 'backgroundColor' => "background: #00c853;"]);
        } catch (\Exception $e) {
            $this->dispatch('alert', ['text' => "Kesalahan: " . $e->getMessage(), 'backgroundColor' => "background: #f44336;"]);
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
        return view('livewire.inspection.fire-inspection', [
            'equipmentMasters' => EquipmentMaster::search($this->type)
                ->byArea($this->searchLocation)
                ->orderBy('specific_location')
                ->limit(10)
                ->get()
        ]);
    }
}
