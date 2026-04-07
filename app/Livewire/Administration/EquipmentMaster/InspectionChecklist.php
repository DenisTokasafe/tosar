<?php

namespace App\Livewire\Administration\EquipmentMaster;

use Livewire\Component;
use App\Models\Location;
use App\Models\InspectionChecklist as InspectionChecklistModel;

class InspectionChecklist extends Component
{
    public $checklists;
    public $location_id;
    public $checklist_id, $equipment_type, $location_keyword;
    public $searchLocation = '';
    public $locations = [];
    public $show_location = false;

    // Array dinamis untuk form
    public $inputs = [['label' => '', 'type' => 'text']];
    public $checks = [''];

    public function render()
    {
        $this->checklists = InspectionChecklistModel::all();
        return view('livewire.administration.equipment-master.inspection-checklist');
    }

    // Menambah baris input kosong
    public function addInput()
    {
        $this->inputs[] = ['label' => '', 'type' => 'text'];
    }
    public function removeInput($index)
    {
        unset($this->inputs[$index]);
        $this->inputs = array_values($this->inputs);
    }

    // Menambah baris check kosong
    public function addCheck()
    {
        $this->checks[] = '';
    }
    public function removeCheck($index)
    {
        unset($this->checks[$index]);
        $this->checks = array_values($this->checks);
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
        $this->location_keyword = $name;
        $this->show_location = false;
    }
    public function save()
    {
        try {
            $this->validate([
                'equipment_type' => 'required',
                'location_keyword' => 'nullable',
                'inputs.*.label' => 'required', // Validasi ke key 'label'
                'inputs.*.type'  => 'required',  // Validasi ke key 'type'
                'checks.*' => 'required',
            ]);

            InspectionChecklistModel::updateOrCreate(
                ['id' => $this->checklist_id],
                [
                    'equipment_type' => $this->equipment_type,
                    'location_keyword' => $this->location_keyword ?? 'Default',
                    'inputs' => $this->inputs,
                    'checks' => $this->checks,
                ]
            );

            session()->flash('message', $this->checklist_id ? 'Updated!' : 'Created!');
            $this->resetForm();
            $this->dispatch('close-checklist-modal');
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors()); // Ini akan memunculkan daftar error di layar jika ada field yang kurang
        }
    }

    public function edit($id)
    {
        $checklist = InspectionChecklistModel::find($id);
        $this->checklist_id = $id;
        $this->equipment_type = $checklist->equipment_type;
        $this->location_keyword = $checklist->location_keyword;
        $this->searchLocation = $checklist->location_keyword;
        $this->inputs = $checklist->inputs;
        $this->checks = $checklist->checks;
    }

    public function delete($id)
    {
        InspectionChecklistModel::find($id)->delete();
    }

    public function resetForm()
    {
        $this->reset(['checklist_id', 'equipment_type', 'location_keyword']);
        // Pastikan strukturnya sesuai dengan yang diharapkan blade
        $this->inputs = [['label' => '', 'type' => 'text']];
        $this->checks = [['label' => '']];
    }
}
