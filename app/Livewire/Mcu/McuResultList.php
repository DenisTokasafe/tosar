<?php

namespace App\Livewire\Mcu;

use App\Models\McuResult;
use Livewire\Component;

class McuResultList extends Component
{
    public function render()
    {
        $mcuResults = McuResult::with([
            'participant.employee',
            'participant.schedule'
        ])
            ->whereIn('workflow_status', ['pending_doctor', 'reviewed'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('livewire.mcu.mcu-result-list', [
            'mcuResults' => $mcuResults
        ]);
    }
}
