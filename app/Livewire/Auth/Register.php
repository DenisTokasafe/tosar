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
use App\Mail\RequestUserLoginMail;
use Illuminate\Support\Facades\Mail;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $name = '';
    public string $email_req = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $no_id = '';
    public string $jenis_kelamin = '';
    public $status = 'department'; // default departemen
    public $departments = [], $showDepartemenDropdown = false, $searchDepartemen = '';
    public $contractors = [], $showContractorDropdown = false, $searchContractor = '';
    public $check_no_id_status = ''; // Menyimpan status pengecekan
    public $check_id = '';



    /**
     * Handle an incoming registration request.
     */
    protected $messages =
    [
        'searchDepartemen.required_if' => 'Departemen wajib diisi.',
        'searchContractor.required_if' => 'Kontraktor wajib diisi.',
        'jenis_kelamin.required' => 'Jenis Kelamin wajib diisi.',
    ];
    public function checkId()
    {
        // Panggil fungsi pengecekan
        $exists = User::where('employee_id', $this->check_id)->exists();

        if ($exists) {
            $this->check_no_id_status = 'Nomor ID sudah terdaftar.';
        } else {
            $this->check_no_id_status = 'Nomor ID belum terdaftar.';
        }
    }
    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['first_name', 'last_name'])) {
            $this->updateNameField();
        }
    }
    protected function updateNameField(): void
    {
        // 1. Pemformatan first_name: Title Case (Yoman Denis)
        $firstName = trim($this->first_name);
        // Ubah ke lowercase dulu, lalu capitalize setiap kata
        $formattedFirstName = ucwords(strtolower($firstName));

        // 2. Pemformatan last_name: Uppercase (BANEA)
        $lastName = trim($this->last_name);
        $formattedLastName = strtoupper($lastName);

        // 3. Gabungkan ke properti 'name' (Format: MARGA, Nama Depan)
        if (!empty($formattedLastName) && !empty($formattedFirstName)) {
            $this->name = "{$formattedLastName}, {$formattedFirstName}";
        } elseif (!empty($formattedLastName)) {
            // Jika hanya Marga yang diisi
            $this->name = $formattedLastName;
        } elseif (!empty($formattedFirstName)) {
            // Jika hanya Nama Depan yang diisi
            $this->name = $formattedFirstName;
        } else {
            $this->name = '';
        }
    }
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
    public function selectDepartment($id, $name)
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
    public function selectContractor($id, $name)
    {
        $this->reset('searchDepartemen');

        $this->searchContractor = $name;
        $this->showContractorDropdown = false;
    }
    public function register(): void
    {
        // Panggil ini lagi untuk memastikan properti 'name' final sebelum validasi
        $this->updateNameField();
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
        // --- LOGIKA BARU UNTUK MENGATASI ERROR DATA TRUNCATED ---
        $genderCode = match ($validated['jenis_kelamin']) {
            'Laki-Laki' => 'L',
            'Perempuan' => 'P',
            default => null, // Tambahkan penanganan jika ada nilai tak terduga
        };
        // Siapkan data untuk User::create
        $dataToCreate = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'employee_id' => $validated['no_id'], // Asumsi 'no_id' masuk ke kolom 'employee_id'
            'gender' => $genderCode, // Asumsi 'jenis_kelamin' masuk ke kolom 'gender'
            'password' => Hash::make($validated['password']),
            'department_name' => $departmentName, // Menyimpan nama Departemen/Kontraktor ke kolom 'department_name'
            // 'role_id' dan field lain (seperti date_commenced) mungkin perlu diisi default/null
        ];

        event(new Registered(($user = User::create($dataToCreate))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
    public function requestUserLogin()
    {
        $this->validate([
            'email_req' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ], [
            'email_req.required' => 'Email wajib diisi untuk request pembuatan user login.',
            'email_req.email' => 'Format email tidak valid.',
        ]);
        try {
            // Kirim email ke Admin (atau ke user itu sendiri jika maksudnya konfirmasi)
            Mail::to('yoman.banea@archimining.com')->send(new RequestUserLoginMail($this->email_req));

            $this->reset('email_req');

            // Menggunakan Flux notification jika Anda sudah menginstalnya,
            // atau tetap menggunakan session flash.
            $this->dispatch('alert', [
                'text' => "Request telah dikirim ke Admin.",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
            ]);
        } catch (\Exception $e) {
            $this->dispatch(
                'alert',
                [
                    'text' => "Gagal mengirim email request user login.",
                    'duration' => 5000,
                    'destination' => '/contact',
                    'newWindow' => true,
                    'close' => true,
                    'backgroundColor' => "linear-gradient(to right, #ff3333, #ff6666)",
                ]
            );
        }
        $this->reset('email_req');
        session()->flash('message', 'Request pembuatan user login telah dikirim. Silakan cek email Anda.');
    }
}
