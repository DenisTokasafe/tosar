<?php

namespace App\Livewire\Inspection;

use Livewire\Component;

class FireInspection extends Component
{
    public $type = 'Fire Extinguisher'; // Default
    public $location, $inspection_date, $inspected_by, $remarks;

    // Tempat menyimpan hasil checklist
    public $conditions = [];

    // Definisi kriteria berdasarkan gambar yang Anda berikan
    public $fields = [
        'Fire Extinguisher' => ['Nozzle', 'Hose', 'Pressure Indicator', 'Head Cap', 'Pin', 'Hook', 'Usage Guide', 'FE Sign'],
        'Eye Wash & Safety Shower' => ['Air', 'Penutup', 'Nozzle', 'Handle', 'Sign', 'Access', 'Kebersihan'],
        'Fire Hose' => ['Hose', 'Reel', 'Nozzle', 'Valve', 'Air', 'Cover'],
        'Fire Hydrant' => ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'],
        'Fire sprinkler system' => ['Line Pipa', 'Main Valve', 'Drain Valve', 'Test valve', 'Alarm', 'Pressure','Access'],
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
        FireInspection::create([
            'type' => $this->type,
            'location' => $this->location,
            'inspection_date' => $this->inspection_date,
            'inspected_by' => $this->inspected_by,
            'conditions' => $this->conditions, // Menyimpan array sebagai JSON
            'remarks' => $this->remarks,
        ]);

        $this->reset(['conditions', 'remarks', 'location']);
        session()->flash('message', 'Inspeksi berhasil dicatat.');
    }
    public function render()
    {
        return view('livewire.inspection.fire-inspection');
    }
}
