<?php

namespace App\Livewire\Mcu;

use Livewire\Component;
use App\Models\McuSchedule;
use App\Models\User;
use Livewire\WithPagination;

class GenerateSchedule extends Component
{
    public $schedule_date;
    public $search = '';
    public $location;
    public $searchSupervisor = [];
    public $showSupervisorDropdown = true; // Selalu true, hide/show diatur oleh AlpineJS di dalam komponen
    public $manualSupervisorMode = false;
    public $manualSupervisorName = [];

    public $activeRowId = null; // Melacak baris (karyawan) mana yang sedang dipilih atasan

    // Ubah format untuk menampung ID Karyawan sekaligus Nomor WA-nya
    // Contoh bentuk array: [ user_id => ['selected' => true, 'wa' => '08123456789'] ]
    public $participantsData = [];
    use WithPagination;
    public function rules()
    {
        return [
            'schedule_date' => 'required|date|after:today',
            'location' => 'required|string',
            // Validasi untuk memastikan minimal 1 karyawan dipilih
            'participantsData' => 'required|array',
        ];
    }
    // --- FUNGSI PELACAK BARIS ---
    public function setActiveRow($employeeId)
    {
        $this->activeRowId = $employeeId;
        $this->manualSupervisorMode = false; // Reset mode manual jika pindah baris
    }

    // --- FUNGSI: SAAT OPTION DIPILIH (clickaction) ---
    public function selectSupervisor($managerId, $managerName)
    {
        if ($this->activeRowId) {
            // 1. Simpan ID ke dalam array utama untuk dikirim ke database
            $this->participantsData[$this->activeRowId]['spv_id'] = $managerId;

            // 2. Tampilkan nama yang dipilih di kolom pencarian agar terlihat rapi
            $this->searchSupervisor[$this->activeRowId] = $managerName;
        }

        // Tutup mode manual jika sedang terbuka
        $this->manualSupervisorMode = false;
    }

    // --- FUNGSI: KLIK TOMBOL "TAMBAH MANUAL" (enableManualAction) ---
    public function enableManualSupervisor()
    {
        $this->manualSupervisorMode = true;
    }

    // --- FUNGSI: SIMPAN DATA MANUAL (addManualAction) ---
    public function addSupervisorManual()
    {
        if ($this->activeRowId && isset($this->manualSupervisorName[$this->activeRowId])) {
            $manualName = $this->manualSupervisorName[$this->activeRowId];

            // 1. Kosongkan ID karena ini data manual, dan simpan namanya
            $this->participantsData[$this->activeRowId]['spv_id'] = null;
            $this->participantsData[$this->activeRowId]['spv_name_manual'] = $manualName;

            // 2. Ganti text di input utama dengan nama manual
            $this->searchSupervisor[$this->activeRowId] = $manualName;

            // 3. Reset state
            $this->manualSupervisorMode = false;
        }
    }
    public function generateJadwal()
    {
        $this->validate();

        // Filter hanya karyawan yang dicentang (selected = true)
        $selectedParticipants = array_filter($this->participantsData, function ($data) {
            return isset($data['selected']) && $data['selected'] == true;
        });

        if (empty($selectedParticipants)) {
            session()->flash('error', 'Pilih minimal 1 peserta MCU.');
            return;
        }

        $schedule = McuSchedule::create([
            'schedule_date' => $this->schedule_date,
            'location' => $this->location,
            'created_by' => auth()->id(),
        ]);

        foreach ($selectedParticipants as $employee_id => $data) {
            $schedule->participants()->create([
                'employee_id' => $employee_id,
                'whatsapp_number' => $data['wa'] ?? null, // Simpan nomor WA manual
                'notification_status' => 'pending'
            ]);
        }

        $this->reset(['schedule_date', 'location', 'participantsData']);
        session()->flash('message', 'Jadwal MCU berhasil dibuat beserta nomor WhatsApp peserta.');
    }

    public function updatingSearch()
    {
        $this->resetPage(); // Fungsi bawaan WithPagination
    }
    public function updatingSearchSupervisor()
    {
        $this->showSupervisorDropdown = true;
    }




    public function render()
    {
        $employees = User::search(trim($this->search))->paginate(10);

        // Ambil text pencarian dari baris yang sedang aktif
        $currentSearch = '';
        if ($this->activeRowId && isset($this->searchSupervisor[$this->activeRowId])) {
            $currentSearch = $this->searchSupervisor[$this->activeRowId];
        }

        // Filter manager berdasarkan ketikan pada baris yang aktif saja
        $managers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Manager', 'Supervisor', 'Department Head']);
        })
            ->when($currentSearch, function ($query) use ($currentSearch) {
                $query->where('name', 'like', '%' . $currentSearch . '%');
            })
            ->limit(10) // Batasi agar dropdown tidak berat
            ->get();

        return view('livewire.mcu.generate-schedule', [
            'employees' => $employees,
            'managers' => $managers

        ]);
    }
}
