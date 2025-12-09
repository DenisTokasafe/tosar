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
    public string $no_id = '';
    public string $jenis_kelamin = '';
    public $status = 'department'; // default departemen
    public $departments = [], $showDepartemenDropdown = false, $searchDepartemen = '';
    public $contractors = [], $showContractorDropdown = false, $searchContractor = '';
    /**
     * Handle an incoming registration request.
     */
    protected $messages =
    [
        'searchDepartemen.required_if' => 'Departemen wajib diisi.',
        'searchContractor.required_if' => 'Kontraktor wajib diisi.',
        'jenis_kelamin.required' => 'Jenis Kelamin wajib diisi.',
    ];

    public function updatedStatus($value)
    {
        if ($value === 'department') {
            // Reset kontraktor jika pindah ke departemen
            $this->resetErrorBag(['searchContractor']);
            $this->reset(['searchContractor', 'contractors']);
        }
        if ($value === 'company') {
            // Reset departemen jika pindah ke kontraktor
            $this->resetErrorBag(['searchDepartemen']);
            $this->reset(['searchDepartemen', 'departments']);
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
    public function selectDepartment($name)
    {
        $this->reset('searchContractor');
        $this->searchDepartemen = $name;
        $this->showDepartemenDropdown = false;
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
    public function selectContractor($name)
    {
        $this->reset('searchDepartemen');

        $this->searchContractor = $name;
        $this->showContractorDropdown = false;
    }
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'no_id' => ['required', 'string', 'max:50', 'unique:users,employee_id'], // Ubah kolom DB jika beda
            'jenis_kelamin' => ['required', 'string', 'in:Laki-Laki,Perempuan'],
            // Validasi wajib isi salah satu (Departemen atau Kontraktor)
            // Kita wajibkan $searchDepartemen jika $status=='department' DAN $searchContractor jika $status=='company'
            'searchDepartemen' => ['required_if:status,department', 'string', 'nullable', 'max:255'],
            'searchContractor' => ['required_if:status,company', 'string', 'nullable', 'max:255'],
            // End Validasi wajib
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Tentukan nilai department_name dari input yang aktif
        $departmentName = ($this->status === 'department') ? $this->searchDepartemen : $this->searchContractor;

        // Siapkan data untuk User::create
        $dataToCreate = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'employee_id' => $validated['no_id'], // Asumsi 'no_id' masuk ke kolom 'employee_id'
            'gender' => $validated['jenis_kelamin'], // Asumsi 'jenis_kelamin' masuk ke kolom 'gender'
            'password' => Hash::make($validated['password']),
            'department_name' => $departmentName, // Menyimpan nama Departemen/Kontraktor ke kolom 'department_name'
            // 'role_id' dan field lain (seperti date_commenced) mungkin perlu diisi default/null
        ];

        event(new Registered(($user = User::create($dataToCreate))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
