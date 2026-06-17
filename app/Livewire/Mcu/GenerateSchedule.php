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
    public $showSupervisorDropdown = [];
    public $manualSupervisorMode = [];
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
    // --- FUNGSI 1: AKTIFKAN MODE MANUAL ---
    public function enableManualSupervisor($employeeId)
    {
        // Hanya baris karyawan ini saja yang berubah jadi mode manual
        $this->manualSupervisorMode[$employeeId] = true;
    }

    // --- FUNGSI 2: SIMPAN DATA MANUAL ---
    public function addSupervisorManual($employeeId)
    {
        if (isset($this->manualSupervisorName[$employeeId])) {
            $manualName = $this->manualSupervisorName[$employeeId];

            // 1. Set ID jadi null (karena manual) dan simpan namanya
            $this->participantsData[$employeeId]['spv_id'] = null;
            $this->participantsData[$employeeId]['spv_name_manual'] = $manualName;

            // 2. Tampilkan nama manual di input text
            $this->searchSupervisor[$employeeId] = $manualName;

            // 3. Matikan mode manual untuk baris ini
            $this->manualSupervisorMode[$employeeId] = false;
        }
    }

    // --- FUNGSI 3: PILIH DARI DROPDOWN ---
    public function selectSupervisor($managerId, $managerName)
    {
        if ($this->activeRowId) {
            $this->participantsData[$this->activeRowId]['spv_id'] = $managerId;
            $this->searchSupervisor[$this->activeRowId] = $managerName;

            $this->manualSupervisorMode[$this->activeRowId] = false;

            // --- TAMBAHKAN INI UNTUK MENUTUP DROPDOWN ---
            $this->showSupervisorDropdown[$this->activeRowId] = false;
        }

        $this->activeRowId = null;
    }
    public function generateJadwal()
    {
        $this->validate();

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
            // 1. Simpan data sesuai struktur terbaru Anda
            $participant = $schedule->participants()->create([
                'employee_id' => $employee_id,
                'whatsapp_number' => $data['wa'] ?? null,
                'supervisor_id' => $data['spv_id'] ?? null,
                'spv_wa_number' => $data['wa_spv'] ?? null,

                // Ubah jadi 'notified' agar tidak dikirim ganda oleh Cron Job H-30
                'notification_status' => 'notified'
            ]);

            // 2. Kirim Notifikasi Langsung ke KARYAWAN
            $employee = User::find($employee_id);
            if ($employee && !empty($participant->whatsapp_number)) {
                $employee->notify(new \App\Notifications\McuReminderNotification($participant, 'new_schedule'));
            }

            // 3. Kirim Notifikasi Langsung ke SUPERVISOR (Bisa dari sistem / manual)
            if (!empty($participant->spv_wa_number)) {
                if ($participant->supervisor_id) {
                    // Jika SPV terdaftar di sistem
                    $spv = User::find($participant->supervisor_id);
                    if ($spv) {
                        $spv->notify(new \App\Notifications\McuReminderNotification($participant, 'new_schedule_spv'));
                    }
                } else {
                    // Jika SPV input manual (Gunakan Anonymous Notification bawaan Laravel)
                    \Illuminate\Support\Facades\Notification::send(
                        new \Illuminate\Notifications\AnonymousNotifiable,
                        new \App\Notifications\McuReminderNotification($participant, 'new_schedule_spv')
                    );
                }
            }
        }

        $this->reset(['schedule_date', 'location', 'participantsData']);
        session()->flash('message', 'Jadwal MCU berhasil dibuat dan notifikasi WA sedang dikirim.');
    }

    public function updatingSearch()
    {
        $this->resetPage(); // Fungsi bawaan WithPagination
    }
    public function updatedSearchSupervisor($value, $key)
    {
        // $key di sini adalah $employee->id
        $this->showSupervisorDropdown[$key] = true;

        // Pastikan baris yang sedang diketik menjadi baris aktif
        $this->activeRowId = $key;
    }
    public function paginationView()
    {
        return 'paginate.pagination';
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
        $managers = User::search($currentSearch)->limit(100)->get();

        return view('livewire.mcu.generate-schedule', [
            'employees' => $employees,
            'managers' => $managers

        ]);
    }
}
