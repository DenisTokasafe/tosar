<?php

namespace App\Livewire\Mcu;

use App\Models\McuSchedule;
use App\Models\User;
use App\Notifications\McuReminderNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
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

    // --- Variabel Dept Head ---
    public $searchDeptHead = [];
    public $showDeptHeadDropdown = [];
    public $manualDeptHeadMode = [];
    public $manualDeptHeadName = [];

    public $activeSpvRowId = null;
    public $activeDeptRowId = null;
    public $participantsData = [];

    use WithPagination;

    public function rules()
    {
        return [
            'schedule_date' => 'required|date|after:today',
            'location' => 'required|string',
            'participantsData' => 'required|array',
        ];
    }

    // --- SUPERVISOR MANUAL ---
    public function enableManualSupervisor($employeeId)
    {
        $this->manualSupervisorMode[$employeeId] = true;
    }

    public function addSupervisorManual($employeeId)
    {
        if (isset($this->manualSupervisorName[$employeeId])) {
            $manualName = $this->manualSupervisorName[$employeeId];

            $this->participantsData[$employeeId]['spv_id'] = null;
            $this->participantsData[$employeeId]['spv_name_manual'] = $manualName;
            $this->searchSupervisor[$employeeId] = $manualName;
            $this->manualSupervisorMode[$employeeId] = false;
        }
    }

    // --- PILIH SUPERVISOR (ANTI RACE-CONDITION) ---
    public function selectSupervisor($managerId, $managerName)
    {
        $employeeId = $this->activeSpvRowId;

        // Fallback jika terjadi delay koneksi Livewire
        if (!$employeeId) {
            $openedDropdowns = array_filter($this->showSupervisorDropdown);
            $employeeId = !empty($openedDropdowns) ? key($openedDropdowns) : null;
        }

        if ($employeeId) {
            $this->participantsData[$employeeId]['spv_id'] = $managerId;
            $this->searchSupervisor[$employeeId] = $managerName;
            $this->manualSupervisorMode[$employeeId] = false;
            $this->showSupervisorDropdown[$employeeId] = false;
        }

        $this->activeSpvRowId = null; // Reset setelah dipilih
    }

    // --- DEPT HEAD MANUAL ---
    public function enableManualDeptHead($employeeId)
    {
        $this->manualDeptHeadMode[$employeeId] = true;
    }

    public function addDeptHeadManual($employeeId)
    {
        if (isset($this->manualDeptHeadName[$employeeId])) {
            $manualName = $this->manualDeptHeadName[$employeeId];

            $this->participantsData[$employeeId]['dept_head_id'] = null;
            $this->participantsData[$employeeId]['dept_head_name_manual'] = $manualName;
            $this->searchDeptHead[$employeeId] = $manualName;
            $this->manualDeptHeadMode[$employeeId] = false;
        }
    }

    // --- PILIH DEPT HEAD (ANTI RACE-CONDITION) ---
    public function selectDeptHead($managerId, $managerName)
    {
        $employeeId = $this->activeDeptRowId;

        // Fallback jika terjadi delay koneksi Livewire
        if (!$employeeId) {
            $openedDropdowns = array_filter($this->showDeptHeadDropdown);
            $employeeId = !empty($openedDropdowns) ? key($openedDropdowns) : null;
        }

        if ($employeeId) {
            $this->participantsData[$employeeId]['dept_head_id'] = $managerId;
            $this->searchDeptHead[$employeeId] = $managerName;
            $this->manualDeptHeadMode[$employeeId] = false;
            $this->showDeptHeadDropdown[$employeeId] = false;
        }

        $this->activeDeptRowId = null; // Reset setelah dipilih
    }

    public function updatedSearchDeptHead($value, $key)
    {
        $this->showDeptHeadDropdown[$key] = true;
        $this->activeDeptRowId = $key;
    }

    public function updatedSearchSupervisor($value, $key)
    {
        $this->showSupervisorDropdown[$key] = true;
        $this->activeSpvRowId = $key;
    }

    // --- GENERATE JADWAL ---
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

        // VALIDASI TAMBAHAN (SAFEGUARD): Mencegah lolos ke query DB jika data kosong
        foreach ($selectedParticipants as $employee_id => $data) {
            // Jika SPV tidak diisi di sistem DAN tidak diisi manual, beri pesan error terarah
            if (empty($data['spv_id']) && empty($data['spv_name_manual'])) {
                $employeeName = User::find($employee_id)->name ?? 'Karyawan';
                session()->flash('error', "Format data Supervisor untuk {$employeeName} belum terpilih dengan benar. Silakan pilih kembali.");
                return;
            }
        }

        $schedule = McuSchedule::create([
            'schedule_date' => $this->schedule_date,
            'location' => $this->location,
            'created_by' => auth()->id(),
        ]);

        foreach ($selectedParticipants as $employee_id => $data) {
            $participant = $schedule->participants()->create([
                'employee_id' => $employee_id,
                'whatsapp_number' => $data['wa'] ?? null,
                'supervisor_id' => $data['spv_id'] ?? null, // Sekarang aman karena ada validasi di atas
                'spv_wa_number' => $data['wa_spv'] ?? null,
                'dept_head_id' => $data['dept_head_id'] ?? null,

                // Masukkan field manual name jika Anda menyimpannya di DB
                'spv_name_manual' => $data['spv_name_manual'] ?? null,
                'dept_head_name_manual' => $data['dept_head_name_manual'] ?? null,

                'notification_status' => 'notified'
            ]);

            // Kirim Notifikasi Karyawan
            $employee = User::find($employee_id);
            if ($employee && !empty($participant->whatsapp_number)) {
                $employee->notify(new McuReminderNotification($participant, 'new_schedule'));
            }

            // Kirim Notifikasi Supervisor
            if (!empty($participant->spv_wa_number)) {
                if ($participant->supervisor_id) {
                    $spv = User::find($participant->supervisor_id);
                    if ($spv) {
                        $spv->notify(new McuReminderNotification($participant, 'new_schedule_spv'));
                    }
                } else {
                    Notification::route('whatsapp', $participant->spv_wa_number)
                        ->notify(new McuReminderNotification($participant, 'new_schedule_spv'));
                }
            }

            // Kirim Notifikasi Dept Head
            if ($participant->dept_head_id) {
                $deptHead = User::find($participant->dept_head_id);
                if ($deptHead && $deptHead->email) {
                    $deptHead->notify(new McuReminderNotification($participant, 'new_schedule_dept_head'));
                }
            }
        }

        $this->reset(['schedule_date', 'location', 'participantsData']);
        session()->flash('message', 'Jadwal MCU berhasil dibuat dan notifikasi WA sedang dikirim.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function paginationView()
    {
        return 'paginate.pagination';
    }

    public function getEmployeeName($id)
    {
        // Cari nama karyawan berdasarkan ID
        // Jika data ada di memori, ambil dari sana. Jika tidak, query ke DB.
        $employee = User::find($id);
        return $employee ? $employee->name : 'Unknown';
    }
    public function render()
    {
        $employees = User::search(trim($this->search))->paginate(10);

        // Pencarian untuk Supervisor
        $currentSearchSpv = '';
        if ($this->activeSpvRowId && isset($this->searchSupervisor[$this->activeSpvRowId])) {
            $currentSearchSpv = $this->searchSupervisor[$this->activeSpvRowId];
        }
        $managers = User::search($currentSearchSpv)->limit(100)->get();

        // Pencarian untuk Dept Head
        $currentSearchDept = '';
        if ($this->activeDeptRowId && isset($this->searchDeptHead[$this->activeDeptRowId])) {
            $currentSearchDept = $this->searchDeptHead[$this->activeDeptRowId];
        }
        $deptHeads = User::search($currentSearchDept)->limit(100)->get();

        return view('livewire.mcu.generate-schedule', [
            'employees' => $employees,
            'managers' => $managers,
            'deptHeads' => $deptHeads
        ]);
    }
}
