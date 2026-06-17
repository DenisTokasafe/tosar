<?php

namespace App\Livewire\Mcu;

use Livewire\Component;
use App\Models\McuSchedule;
use App\Models\User;
use Livewire\WithPagination;

class GenerateSchedule extends Component
{
    public $schedule_date, $search;
    public $location;

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

    public function render()
    {
        $employees = User::search(trim($this->search))->paginate(10);

        return view('livewire.mcu.generate-schedule', [
            'employees' => $employees
        ]);
    }
}
