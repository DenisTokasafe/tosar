<?php

namespace App\Livewire\Incident;

use App\Models\EventSubType;
use App\Models\EventType;
use Livewire\Component;

class Create extends Component
{
    public $event_type_id;
    public function render()
    {
        return view('livewire.incident.create', [
            'eventTypes' => EventType::onlyIncidents()->get(),
            'eventSubTypes' => EventSubType::where('event_type_id', $this->event_type_id)->get(),
        ]);
    }
}
