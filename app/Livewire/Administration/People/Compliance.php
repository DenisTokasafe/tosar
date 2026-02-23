<?php

namespace App\Livewire\Administration\People;

use App\Models\Compliance as ModelsCompliance;
use App\Models\User;
use Livewire\Component;

class Compliance extends Component
{
    public $userId;
    public function mount($id)
    {
        $user = User::findOrFail($id);

        // ❗ PINDAHKAN INI KE ATAS: Set $this->userId DULU
        $this->userId = $user->id;
    }
    public function render()
    {
        return view('livewire.administration.people.compliance', [
            'compliances' => ModelsCompliance::where('user_id', $this->userId)->with('master')->get()
        ]);
    }
}
