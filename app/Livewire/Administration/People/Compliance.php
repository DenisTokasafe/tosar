<?php

namespace App\Livewire\Administration\People;

use App\Models\Compliance as ModelsCompliance;
use App\Models\ComplianceMaster;
use App\Models\User;
use Livewire\Component;

class Compliance extends Component
{
    public $userId;
    public $compliance_class;
    public $compliance_name;
    public function mount($id)
    {
        $user = User::findOrFail($id);

        // ❗ PINDAHKAN INI KE ATAS: Set $this->userId DULU
        $this->userId = $user->id;
    }
    public function getExistingClassesProperty()
    {
        return ComplianceMaster::select('class')
            ->distinct()
            ->whereNotNull('class')
            ->orderBy('class', 'asc')
            ->pluck('class');
    }
    public function getExistingNameProperty()
    {
        return ComplianceMaster::select('name')
            ->distinct()
            ->whereNotNull('name')
            ->orderBy('name', 'asc')
            ->pluck('name');
    }
    public function render()
    {
        return view('livewire.administration.people.compliance', [
            'compliances' => ModelsCompliance::where('user_id', $this->userId)->with('master')->get()
        ]);
    }
}
