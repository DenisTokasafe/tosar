<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use App\Models\Contractor;
use App\Models\Department;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[Validate('required_without:contractor_id')]
    public $department_id;
    #[Validate('required_without:department_id')]
    public $contractor_id;
    public  $showMpderatorDropdown = false, $searchModerator = '';
    public $status = 'department'; // default departemen
    public $departments = [], $showDepartemenDropdown = false, $searchDepartemen = '';
    public $contractors = [], $showContractorDropdown = false, $searchContractor = '';
    /**
     * Handle an incoming registration request.
     */
    protected $messages =
    [
        'department_id.required_without' => 'Departemen wajib dipilih jika kontraktor tidak diisi.',
        'contractor_id.required_without' => 'Kontraktor wajib dipilih jika departemen tidak diisi.',
    ];

    public function updatedStatus($value)
    {
        if ($value === 'department') {
            // Reset kontraktor jika pindah ke departemen
            $this->resetErrorBag(['contractor_id']);
            $this->reset(['contractor_id', 'searchContractor', 'contractors']);
        }
        if ($value === 'company') {
            // Reset departemen jika pindah ke kontraktor
            $this->resetErrorBag(['department_id']);
            $this->reset(['department_id', 'searchDepartemen', 'departments']);
        }
    }


    public function updatedSearchDepartemen()
    {
        if (strlen($this->searchDepartemen) > 1) {
            $this->departments = Department::where('department_name', 'like', '%' . $this->searchDepartemen . '%')
                ->orderBy('department_name')
                ->limit(10)
                ->get();
            $this->showDepartemenDropdown = true;
        } else {
            $this->departments = [];
            $this->showDepartemenDropdown = false;
        }
    }
    public function selectDepartment($id, $name)
    {
        $this->department_id = $id;
        $this->searchDepartemen = $name;
        $this->showDepartemenDropdown = false;
        $this->validateOnly('department_id');
    }
    public function updatedSearchContractor()
    {
        if (strlen($this->searchContractor) > 1) {
            $this->contractors = Contractor::query()
                ->where('contractor_name', 'like', '%' . $this->searchContractor . '%')
                ->orderBy('contractor_name')
                ->limit(10)
                ->get();
            $this->showContractorDropdown = true;
        } else {
            $this->contractors = [];
            $this->showContractorDropdown = true;
        }
    }
    public function selectContractor($id, $name)
    {
        $this->reset('searchDepartemen', 'department_id');
        $this->contractor_id = $id;
        $this->searchContractor = $name;
        $this->showContractorDropdown = false;
        $this->validateOnly('contractor_id');
    }
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
