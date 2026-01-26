<?php

namespace App\Livewire\Inspection;

use App\Models\User;
use Livewire\Component;
use App\Helpers\FileHelper;
use Livewire\Attributes\Validate;

class FireInspection extends Component
{
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
    // Tempat menyimpan hasil checklist
    public $conditions = [];

    public function rules()
    {
        return [
            'location'        => 'required|string|max:255',
            'inspection_date' => 'required|date',
            'inspected_by'    => 'required|string|max:255',
            'conditions'      => 'required|array',
            'type'            => 'required|string',
            'area'            => 'required|string|max:255',
            'dokumentasi'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ];
    }

    protected $messages = [
        'location.required'        => 'Lokasi inspeksi wajib diisi.',
        'inspection_date.required' => 'Tanggal inspeksi wajib diisi.',
        'inspected_by.required'    => 'Nama pemeriksa wajib diisi.',
        'conditions.required'     => 'Checklist kondisi alat wajib diisi.',
        'type.required'           => 'Jenis alat wajib diisi.',
        'area.required'           => 'Area wajib diisi.',
        'dokumentasi.file'  => 'File dokumentasi harus berupa berkas yang valid.',
        'dokumentasi.mimes' => 'File dokumentasi hanya boleh dalam format JPG, JPEG, PNG, DOC, DOCX atau PDF.',
        'dokumentasi.max'   => 'Ukuran file dokumentasi maksimal 2 MB.',
    ];

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

    // Definisi kriteria berdasarkan gambar yang Anda berikan
    public $fields = [
        'Fire Extinguisher' => ['FE No.', 'FE Type', 'Capacity', 'Nozzle', 'Hose', 'Pressure Indicator', 'Head Cap', 'Pin', 'Hook', 'Usage Guide', 'FE Sign'],
        'Eye Wash & Safety Shower' => ['Air', 'Penutup', 'Nozzle', 'Handle', 'Sign', 'Access', 'Kebersihan'],
        'Fire Hose Reel' => ['Hose', 'Reel', 'Nozzle', 'Valve', 'Air', 'Cover'],
        'Fire Hose Cabinet' => ['Box No.', 'Box', 'Hose', 'Rack', 'Nozzle', 'Valve'],
        'Fire Hydrant' => ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'],
        'Fire sprinkler system' => ['Line Pipa', 'Main Valve', 'Drain Valve', 'Test valve', 'Alarm', 'Pressure', 'Access'],
        'Ring Buoy' => ['Ring Buoy', 'Access', 'Tempat Ring Buoy', 'Tali'],
        'Muster Point' => ['Access', 'Visibility', 'Colour', 'Condition of Board', 'Condition of Pole', 'Letter'],
    ];

    public function updatedType($value)
    {
        // Reset checklist saat jenis alat diganti
        $this->conditions = [];
        foreach ($this->fields[$value] as $field) {
            $this->conditions[$field] = 'Good'; // Default status
        }
    }

    public function save()
    {
        if ($this->dokumentasi) {
            $docCorrectivePath = FileHelper::compressAndStore($this->dokumentasi, 'inspections/documents');
        }
        FireInspection::create([
            'type' => $this->type,
            'location' => $this->location,
            'area' => $this->area,
            'dokumentasi' => $docCorrectivePath,
            'inspection_date' => $this->inspection_date,
            'inspected_by' => $this->inspected_by,
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
