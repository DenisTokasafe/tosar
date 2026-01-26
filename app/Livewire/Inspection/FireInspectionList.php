<?php

namespace App\Livewire\Inspection;

use Livewire\Component;
use App\Models\FireProtection;

class FireInspectionList extends Component
{
    public function render()
    {
        return view('livewire.inspection.fire-inspection-list',[
            'inspections' => FireProtection::latest()->paginate(10)
        ]);
    }
}
