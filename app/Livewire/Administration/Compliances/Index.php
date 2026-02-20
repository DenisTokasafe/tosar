<?php

namespace App\Livewire\Administration\Compliances;

use App\Models\ComplianceMaster;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public function render()
    {
        return view('livewire.administration.compliances.index',[
             'ComplianceMaster' => ComplianceMaster::paginate(20)
        ]);
    }
}
