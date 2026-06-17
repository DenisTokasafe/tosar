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
    public $managerSearch = ''; // Search term untuk dropdown
    public $activeRowId = null; // Melacak baris (karyawan) mana yang sedang dipilih atasan
    public $activeField = null; // Melacak apakah ini untuk 'spv_id' atau 'dept_head_id'

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

    public function selectManager($managerId, $managerName)
    {
        // Update data peserta berdasarkan baris dan kolom yang sedang aktif
        if ($this->activeRowId && $this->activeField) {
            $this->participantsData[$this->activeRowId][$this->activeField] = $managerId;

            // Simpan nama/label agar user tahu siapa yang dipilih (optional, untuk tampilan)
            $this->participantsData[$this->activeRowId][$this->activeField . '_name'] = $managerName;
        }

        // Reset search agar dropdown tertutup/bersih
        $this->managerSearch = '';
        $this->activeRowId = null;
        $this->activeField = null;
    }

    public function render()
    {
        $employees = User::search(trim($this->search))->paginate(10);
        $managers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Manager', 'Supervisor', 'Department Head']);
        })->where('name', 'like', '%' . $this->managerSearch . '%')
            ->get();

        return view('livewire.mcu.generate-schedule', [
            'employees' => $employees,
            'managers' => $managers

        ]);
    }
}
