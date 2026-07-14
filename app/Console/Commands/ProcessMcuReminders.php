<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\McuParticipant;
use App\Notifications\McuReminderNotification;
use Carbon\Carbon;

class ProcessMcuReminders extends Command
{
    protected $signature = 'mcu:process-reminders';
    protected $description = 'Proses notifikasi H-30, H-14, H-7, H-1 untuk MCU';

    public function handle()
    {
        $today = Carbon::today();

        // Ambil semua peserta yang jadwal MCU-nya belum lewat
        $participants = McuParticipant::with(['schedule', 'employee.department.head', 'employee.supervisor'])
            ->whereHas('schedule', function ($q) use ($today) {
                $q->where('schedule_date', '>=', $today);
            })->get();

        foreach ($participants as $p) {
            $daysLeft = $today->diffInDays($p->schedule->schedule_date, false);

            if ($daysLeft == 30 && $p->notification_status === 'pending') {
                $this->sendNotifications($p, 'h-30', 'notified');
            } elseif ($daysLeft == 14 && $p->notification_status === 'notified') {
                $this->sendNotifications($p, 'h-14', 'reminder_1');
            } elseif ($daysLeft == 7 && $p->notification_status === 'reminder_1') {
                $this->sendNotifications($p, 'h-7', 'reminder_2');
            } elseif ($daysLeft == 1 && $p->notification_status === 'reminder_2') {
                $this->sendNotifications($p, 'h-1', 'final_reminder');
            }
        }
    }

    private function sendNotifications(McuParticipant $participant, string $type, string $nextStatus)
    {
        $employee = $participant->employee;
        $supervisor = $participant->supervisor;

        $employee->notify(new McuReminderNotification($participant, $type));
        // Logic eskalasi sesuai flowchart
        if (in_array($type, ['h-30', 'h-14', 'h-7', 'h-1'])) {
            // Notifikasi ke Karyawan
            $employee->notify(new McuReminderNotification($participant, $type));
            // Asumsi relasi user ke department head ada
            $employee->departmentHead?->notify(new McuReminderNotification($participant, $type));
        }

        if (in_array($type, ['h-7', 'h-1'])) {
            $supervisor->notify(new McuReminderNotification($participant, $type));
        }

        // Update status di database
        $participant->update(['notification_status' => $nextStatus]);
    }
}
