<?php

namespace App\Livewire\Inspection;

use App\Models\User;
use Livewire\Component;
use App\Models\Location;
use App\Helpers\FileHelper;
use Livewire\WithFileUploads;
use App\Models\FireProtection;
use Livewire\Attributes\Validate;
use Livewire\Volt\Compilers\Mount;

class FireInspection extends Component
{
    use WithFileUploads;
    public $type = 'Fire Extinguisher'; // Default
    public $location, $inspection_date, $inspected_by, $remarks, $area;
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx')]
    public $dokumentasi;
    public $searchResponsibility = '';
    public $pelapors = [];
    public $showPelaporDropdown = false;
    public $manualPelaporMode = false;
    public $manualPelaporName = '';
    public $responsible_id;
    public $inspected_users = [];
    // Untuk fitur pencarian lokasi
    public $location_id;
    public $show_location = false;
    public $locations = [];
    public $searchLocation = '';
    // Tempat menyimpan hasil checklist
    public $conditions = [];

    public function mount()
    {
        // Inisialisasi kondisi berdasarkan tipe default
        $this->updatedType($this->type);
    }
    public function rules()
    {
        $baseRules = [
            'location'        => 'required|string|max:255',
            'inspection_date' => 'required|date',
            'inspected_users' => 'required|array|min:1', // Menggunakan array user dari jawaban sebelumnya
            'conditions'      => 'required|array',
            'type'            => 'required|string',
            'area'            => 'required|string|max:255',
            'dokumentasi'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ];

        // Tambahkan validasi otomatis untuk setiap item di dalam conditions
        if (isset($this->fields[$this->type]['inputs'])) {
            foreach ($this->fields[$this->type]['inputs'] as $input) {
                $baseRules["conditions.{$input}"] = 'required';
            }
        }

        // if (isset($this->fields[$this->type]['checks'])) {
        //     foreach ($this->fields[$this->type]['checks'] as $check) {
        //         $baseRules["conditions.{$check}"] = 'required';
        //     }
        // }

        return $baseRules;
    }

    protected function messages()
    {
        return [
            'location.required'        => 'Lokasi inspeksi wajib diisi.',
            'inspection_date.required' => 'Tanggal inspeksi wajib diisi.',
            'inspected_users.required' => 'Minimal satu orang pemeriksa wajib dipilih.',
            'conditions.required'      => 'Checklist kondisi alat wajib diisi.',
            'conditions.*.required'    => 'Semua poin checklist wajib diisi/pilih.', // Pesan untuk item dinamis
            'type.required'            => 'Jenis alat wajib diisi.',
            'area.required'            => 'Area wajib diisi.',
            'dokumentasi.max'          => 'Ukuran file dokumentasi maksimal 2 MB.',
            // ... sisa pesan Anda
        ];
    }

    public function updatedSearchResponsibility()
    {
        $this->reset('manualPelaporName');
        $this->manualPelaporMode = false;
        if (strlen($this->searchResponsibility) > 1) {
            $this->pelapors = User::where('name', 'like', '%' . $this->searchResponsibility . '%')
                ->orderBy('name')
                ->limit(50)
                ->get();
            $this->showPelaporDropdown = true;
        } else {
            $this->pelapors = [];
            $this->showPelaporDropdown = false;
        }
    }
    public function selectPelapor($id, $name)
    {
        // $this->searchResponsibility = $name;
        $this->showPelaporDropdown = false;
        $this->manualPelaporMode = false;

        if (!collect($this->inspected_users)->contains('id', $id)) {
            $this->inspected_users[] = [
                'id' => $id,
                'name' => $name
            ];
        }
        $this->reset(['searchResponsibility', 'showPelaporDropdown']);
    }
    public function enableManualPelapor()
    {
        $this->manualPelaporMode = true;
        $this->manualPelaporName = $this->searchResponsibility; // isi default sama dengan isi search
        // Masukkan data manual (id null)
        $this->inspected_users[] = [
            'id' => null,
            'name' => $this->manualPelaporName
        ];
        $this->reset(['manualPelaporName', 'searchResponsibility', 'showPelaporDropdown', 'manualPelaporMode']);
    }
    public function updatedManualPelaporName($value)
    {
        $this->responsible_id = null;
    }
    public function removeInspectedUser($index)
    {
        unset($this->inspected_users[$index]);
        $this->inspected_users = array_values($this->inspected_users); // re-index array
    }

    public function addPelaporManual()
    {
        $this->searchResponsibility = $this->manualPelaporName;
        $this->showPelaporDropdown = false;
        $this->responsible_id = null;
    }

    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(50)
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
        $this->validateOnly('location_id');
    }

    // Definisi kriteria berdasarkan gambar yang Anda berikan
    public $fields = [
        'Fire Extinguisher' => [
            'inputs' => ['FE No', 'FE Type', 'Capacity'],
            'checks' => ['Nozzle', 'Hose', 'Pressure Indicator', 'Head Cap', 'Pin', 'Hook', 'Usage Guide', 'FE Sign']
        ],
        'Fire Hose Cabinet' => [
            'inputs' => ['Box No', 'Box'],
            'checks' => ['Hose', 'Rack', 'Nozzle', 'Valve']
        ],
        'Muster Point' => [
            'inputs' => ['ID Muster Point'],
            'checks' => ['Access', 'Visibility', 'Colour', 'Condition of Board', 'Condition of Pole', 'Letter'],
        ],
        'Fire Hydrant' => [
            'inputs' => ['Hydrant No'],
            'checks' => ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'],
        ],

        'Eye Wash & Safety Shower' => [
            'inputs' => ['E&S No'],
            'checks' => ['Access', 'Signage', 'Water Flow', 'Hose Condition', 'Nozzle Condition', 'Drainage'],
        ],
        'Fire Hose Reel' => [
            'inputs' => ['Hose Reel No'],
            'checks' => ['Hose', 'Reel', 'Nozzle', 'Valve', 'Air', 'Cover'],
        ],
        'Fire sprinkler system' => [
            'inputs' => ['Sprinkler No'],
            'checks' => ['Line Pipa', 'Main Valve', 'Drain Valve', 'Test valve', 'Alarm', 'Pressure', 'Access'],
        ],
        'Ring Buoy' => [
            'inputs' => ['Ring Buoy No'],
            'checks' => ['Ring Buoy', 'Access', 'Tempat Ring Buoy', 'Tali'],
        ],
    ];

    public function updatedType($value)
    {
        // 1. Reset semua data kondisi sebelumnya
        $this->conditions = [];

        // 2. Inisialisasi untuk field yang berupa input teks (kosongkan dulu)
        if (isset($this->fields[$value]['inputs'])) {
            foreach ($this->fields[$value]['inputs'] as $inputField) {
                $this->conditions[$inputField] = '';
            }
        }

        // Inisialisasi checklist menjadi TRUE secara default
        if (isset($this->fields[$value]['checks'])) {
            foreach ($this->fields[$value]['checks'] as $checkField) {
                // Gunakan boolean true murni
                $this->conditions[$checkField] = true;
            }
        }
    }

    public function save()
    {
        $this->validate();
        $inspectedByString = is_array($this->inspected_users)
            ? implode('|', array_column($this->inspected_users, 'name'))
            : $this->inspected_users;
        $documentationPath = null;
        if ($this->dokumentasi) {
            $documentationPath = FileHelper::compressAndStore($this->dokumentasi, 'inspections/documents');
        }
        FireProtection::create([
            'type' => $this->type,
            'location' => $this->location,
            'area' => $this->area,
            'documentation_path' => $documentationPath,
            'inspection_date' => $this->inspection_date,
            'inspected_by' => $inspectedByString,
            'conditions' => $this->conditions, // Menyimpan array sebagai JSON
            'remarks' => $this->remarks,
        ]);

        $this->resetForm();
        $this->dispatch('alert', [
            'text' => "Data Inspeksi berhasil disimpan!",
            'duration' => 5000,
            'destination' => '/contact',
            'newWindow' => true,
            'close' => true,
            'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
        ]);
    }
    public function resetForm()
    {
        $this->location = null;
        $this->inspection_date = null;
        $this->inspected_by = null;
        $this->remarks = null;
        $this->area = null;
        $this->dokumentasi = null;
        $this->conditions = [];
    }
    public function render()
    {
        return view('livewire.inspection.fire-inspection');
    }
}
