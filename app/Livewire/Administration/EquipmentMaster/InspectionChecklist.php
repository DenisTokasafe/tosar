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
    public $inputs = [''];
    public $checks = [''];
    public $inputs_req = ['']; // Variabel baru yang Anda minta

    public function render()
    {
        $this->checklists = InspectionChecklistModel::all();
        return view('livewire.administration.equipment-master.inspection-checklist');
    }

    // --- Logika untuk Inputs ---
    public function addInput()
    {
        $this->inputs[] = '';
    }
    public function removeInput($index)
    {
        unset($this->inputs[$index]);
        $this->inputs = array_values($this->inputs);
    }

    // --- Logika untuk Checks ---
    public function addCheck()
    {
        $this->checks[] = '';
    }
    public function removeCheck($index)
    {
        unset($this->checks[$index]);
        $this->checks = array_values($this->checks);
    }

    // --- Logika untuk Inputs Req (Baru) ---
    public function addInputReq()
    {
        $this->inputs_req[] = '';
    }

    public function removeInputReq($index)
    {
        unset($this->inputs_req[$index]);
        $this->inputs_req = array_values($this->inputs_req);
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
        $this->validate([
            'equipment_type' => 'required',
            'location_keyword' => 'nullable',
            'inputs.*' => 'required',
            'checks.*' => 'required',
            'inputs_req.*' => 'required', // Validasi untuk inputs_req
        ]);

        InspectionChecklistModel::updateOrCreate(
            ['id' => $this->checklist_id],
            [
                'equipment_type' => $this->equipment_type,
                'location_keyword' => $this->location_keyword ?? 'Default',
                'inputs' => $this->inputs,
                'checks' => $this->checks,
                'inputs_req' => $this->inputs_req, // Pastikan kolom ini ada di database/migration
            ]
        );

        session()->flash('message', $this->checklist_id ? 'Updated!' : 'Created!');
        $this->resetForm();
        $this->dispatch('close-checklist-modal');
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
        // Ambil data inputs_req dari database, pastikan ada nilai default jika null
        $this->inputs_req = $checklist->inputs_req ?? [''];
    }

    public function delete($id)
    {
        InspectionChecklistModel::find($id)->delete();
    }

    public function resetForm()
    {
        $this->reset(['checklist_id', 'equipment_type', 'location_keyword', 'inputs', 'checks', 'inputs_req']);
        $this->inputs = [''];
        $this->checks = [''];
        $this->inputs_req = [''];
    }
}
