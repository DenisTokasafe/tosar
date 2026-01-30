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
    public $dokumentasi;

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

    // Tempat menyimpan hasil checklist dan technical data
    public $conditions = [];

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
            'checks' => ['Access', 'Signage', 'Water Flow', 'Hose Condition', 'Nozzle Condition', 'Drainage'],
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
    }

    public function selectLocation($id, $name)
    {
        $this->location_id = $id;
        $this->searchLocation = $name;
        $this->area = $name;
        $this->show_location = false;

        if ($this->type === 'Fire Hydrant' && str_contains(strtolower($this->searchLocation), 'maesa camp')) {
            $this->fields['Fire Hydrant']['checks'] = ['Box', 'Hose', 'Rack', 'Valve', 'Nozel'];
        } else {
            // Kembalikan ke default jika bukan Maesa Camp
            $this->fields['Fire Hydrant']['checks'] = ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'];
        }

        // Ambil semua alat
       $this->selected_location_specific = EquipmentMaster::where('location_id', $id)
        ->where('type', $this->type)
        ->get();

        $this->conditions = []; // Reset

            $this->initializeConditions();

    }
    private function initializeConditions()
    {
        foreach ($this->selected_location_specific as $master) {
            foreach ($this->fields[$this->type]['checks'] as $check) {
                if (!isset($this->conditions[$master->id][$check])) {
                    $this->conditions[$master->id][$check] = true;
                }
            }
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
        if ($value === 'Fire Hydrant' && str_contains(strtolower($this->searchLocation), 'maesa camp')) {
            $this->fields['Fire Hydrant']['checks'] = ['Box', 'Hose', 'Rack', 'Valve', 'Nozel'];
        } else {
            // Kembalikan ke default jika bukan Maesa Camp
            $this->fields['Fire Hydrant']['checks'] = ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'];
        }
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
    public function save()
    {
        $this->validate([
            'inspection_date' => 'required|date',
            'location_id'     => 'required',
            'inspected_users' => 'required|array|min:1',
            'conditions'      => 'required|array|min:1',
        ]);

        try {
            $inspectedByString = implode('|', array_column($this->inspected_users, 'name'));

            $documentationPath = null;
            if ($this->dokumentasi) {
                $documentationPath = \App\Helpers\FileHelper::compressAndStore($this->dokumentasi, 'inspections/documents');
            }

            DB::transaction(function () use ($inspectedByString, $documentationPath) {
                foreach ($this->conditions as $equipmentMasterId => $dataKondisi) {

                    // Ambil remarks khusus baris ini dari array conditions
                    $rowRemarks = $dataKondisi['remarks'] ?? null;

                    // Opsional: Hapus key 'remarks' agar tidak ikut tersimpan di kolom JSON 'conditions'
                    $cleanConditions = collect($dataKondisi)->forget('remarks')->toArray();

                    FireProtection::create([
                        'equipment_master_id' => $equipmentMasterId,
                        'documentation_path'  => $documentationPath,
                        'inspection_date'     => $this->inspection_date,
                        'inspected_by'        => $inspectedByString,
                        'conditions'          => $cleanConditions, // Data teknis + hasil checklist
                        'remarks'             => $rowRemarks,      // Catatan spesifik per alat
                    ]);
                }
            });

            $this->resetForm();
            $this->dispatch('alert', ['text' => "Data inspeksi berhasil disimpan!", 'backgroundColor' => "background: #00c853;"]);
        } catch (\Exception $e) {
            $this->dispatch('alert', ['text' => "Kesalahan: " . $e->getMessage(), 'backgroundColor' => "background: #f44336;"]);
        }
    }

    public function resetForm()
    {
        $this->reset(['location', 'remarks', 'dokumentasi', 'inspected_users', 'equipment_master_id', 'conditions']);
        $this->inspection_date = now()->format('Y-m-d');
        $this->updatedType($this->type);
    }

    public function render()
    {
        return view('livewire.inspection.fire-inspection');
    }
}
