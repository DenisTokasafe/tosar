<?php

namespace App\Livewire\Incident;

use App\Models\EventSubType;
use App\Models\EventType;
use Livewire\Component;

class Create extends Component
{
    public $event_type_id,
        $event_sub_type_id,
        $description,
        $location,
        $date_time;

     protected $rules = [
        'event_type_id' => 'required|exists:event_types,id',
        'event_sub_type_id' => 'required|exists:event_sub_types,id',
        'description' => 'required|string',
        'location' => 'required|string',
        'date_time' => 'required|date',
    ];
    public function render()
    {
        return view('livewire.incident.create', [
            'eventTypes' => EventType::onlyIncidents()->get(),
            'eventSubTypes' => EventSubType::where('event_type_id', $this->event_type_id)->get(),
        ]);
    }
}
