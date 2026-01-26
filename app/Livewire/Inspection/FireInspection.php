<?php

namespace App\Livewire\Inspection;

use Livewire\Component;
use App\Helpers\FileHelper;
use Livewire\Attributes\Validate;

class FireInspection extends Component
{
    public $type = 'Fire Extinguisher'; // Default
    public $location, $inspection_date, $inspected_by, $remarks,$area;
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx')]
    public $dokumentasi;
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

    // Definisi kriteria berdasarkan gambar yang Anda berikan
    public $fields = [
        'Fire Extinguisher' => ['Nozzle', 'Hose', 'Pressure Indicator', 'Head Cap', 'Pin', 'Hook', 'Usage Guide', 'FE Sign'],
        'Eye Wash & Safety Shower' => ['Air', 'Penutup', 'Nozzle', 'Handle', 'Sign', 'Access', 'Kebersihan'],
        'Fire Hose' => ['Hose', 'Reel', 'Nozzle', 'Valve', 'Air', 'Cover'],
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
