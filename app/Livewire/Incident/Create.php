<?php

namespace App\Livewire\Incident;

use App\Models\EventSubType;
use App\Models\EventType;
use App\Models\Location;
use App\Models\UnsafeAct;
use App\Models\UnsafeCondition;
use Livewire\Component;

class Create extends Component
{
    public $event_type_id,
        $event_sub_type_id,
        $description,
        $location_id,
        $location_spesific,
        $documentation,
        $documentation_description,
        $date_time;
    public $keyWord = 'kta';
    public $locations = [];
    public $searchLocation = '';
    public $show_location = false;
    public $currentStep = 1;
    public $totalSteps = 3;

    protected $rules = [
        'event_type_id' => 'required|exists:event_types,id',
        'event_sub_type_id' => 'required|exists:event_sub_types,id',
        'description' => 'required|string',
        'location' => 'required|string',
        'date_time' => 'required|date',
    ];
    public function nextStep()
    {
        $this->validate($this->rules()[$this->currentStep]);
        $this->currentStep++;
    }
    public function previousStep()
    {
        $this->currentStep--;
    }
    // Search Location
     public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(80)
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
        $this->show_location = false;
    }
    public function render()
    {
        return view('livewire.incident.create', [
            'eventTypes' => EventType::onlyIncidents()->get(),
            'eventSubTypes' => EventSubType::where('event_type_id', $this->event_type_id)->get(),
            'ktas' => UnsafeCondition::latest()->get(),
            'ttas' => UnsafeAct::latest()->get(),
        ]);
    }
}
