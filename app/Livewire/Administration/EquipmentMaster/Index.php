<?php

namespace App\Livewire\Administration\EquipmentMaster;

use Livewire\Component;
use App\Models\Location;
use App\Models\EquipmentMaster;

class Index extends Component
{
    use WithPagination;

    public $type, $location_id, $specific_location, $is_active = true;
    public $technical_data = []; // Untuk menyimpan key-value dinamis (FE No, Capacity, dll)
    public $newKey, $newValue; // Input sementara untuk menambah baris JSON
    public $selected_id, $search;
    public $isEdit = false;

    protected $rules = [
        'type' => 'required',
        'location_id' => 'required|exists:locations,id',
        'technical_data' => 'required|array|min:1',
    ];

    // Menambah baris spesifikasi baru (misal: "FE No" -> "PH001")
    public function addTechnicalField()
    {
        if ($this->newKey && $this->newValue) {
            $this->technical_data[$this->newKey] = $this->newValue;
            $this->reset(['newKey', 'newValue']);
        }
    }

    public function removeTechnicalField($key)
    {
        unset($this->technical_data[$key]);
    }

    public function save()
    {
        $this->validate();

        EquipmentMaster::updateOrCreate(['id' => $this->selected_id], [
            'type' => $this->type,
            'location_id' => $this->location_id,
            'specific_location' => $this->specific_location,
            'technical_data' => $this->technical_data,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('alert', ['text' => $this->isEdit ? 'Data diperbarui!' : 'Data ditambah!']);
        $this->resetForm();
    }

    public function edit($id)
    {
        $data = EquipmentMaster::findOrFail($id);
        $this->selected_id = $id;
        $this->type = $data->type;
        $this->location_id = $data->location_id;
        $this->specific_location = $data->specific_location;
        $this->technical_data = $data->technical_data;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        EquipmentMaster::destroy($id);
        $this->dispatch('alert', ['text' => 'Data berhasil dihapus!']);
    }

    public function resetForm()
    {
        $this->reset(['type', 'location_id', 'specific_location', 'technical_data', 'selected_id', 'isEdit']);
    }
    public function render()
    {
        return view('livewire.administration.equipment-master.index',[
            'equipments' => EquipmentMaster::with('location')
                ->where('type', 'like', "%{$this->search}%")
                ->paginate(10),
            'locations' => Location::all(),
            'available_types' => ['Fire Extinguisher', 'Fire Hydrant', 'Fire Hose Reel', 'Eyewash & Safety Shower', 'Muster Point']
        ]);
    }
}
