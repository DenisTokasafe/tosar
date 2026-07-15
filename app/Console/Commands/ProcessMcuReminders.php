<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\McuParticipant;
use App\Notifications\McuReminderNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class ProcessMcuReminders extends Command
{
    protected $signature = 'mcu:process-reminders';
    protected $description = 'Proses notifikasi h-30, h-14, h-7, h-1 untuk MCU';

    public function handle()
    {
        // 1. Peta eskalasi aturan reminder
        $reminders = [
            30 => ['type' => 'h-30', 'current' => 'pending',    'next' => 'notified'],
            14 => ['type' => 'h-14', 'current' => 'notified',   'next' => 'reminder_1'],
            7  => ['type' => 'h-7',  'current' => 'reminder_1', 'next' => 'reminder_2'],
            1  => ['type' => 'h-1',  'current' => 'reminder_2', 'next' => 'final_reminder'],
        ];

        foreach ($reminders as $days => $config) {
            $targetDate = Carbon::today()->addDays($days)->toDateString();

            // 2. Filter langsung di level Database & gunakan chunk agar hemat memori
            McuParticipant::with(['schedule', 'employee', 'supervisor', 'deptHead'])
                ->where('notification_status', $config['current'])
                ->whereHas('schedule', function ($q) use ($targetDate) {
                    $q->whereDate('schedule_date', $targetDate);
                })
                ->chunkById(100, function ($participants) use ($config) {
                    foreach ($participants as $participant) {
                        $this->sendNotifications($participant, $config['type'], $config['next']);
                    }
                });
        }

        $this->info('Proses reminder MCU berhasil dijalankan.');
    }

    // 2. Ubah pemanggilan variabel di dalam sendNotifications():
    private function sendNotifications(McuParticipant $participant, string $type, string $nextStatus)
    {
        $employee = $participant->employee;

        // AMBIL LANGSUNG DARI PARTICIPANT SESAAT SETELAH DI-EAGER LOAD
        $supervisor = $participant->supervisor;

        // Jika kolom dept_head_id ada di tabel, gunakan $participant->deptHead
        // Jika tidak, fallback ke relasi departemen karyawan
        $departmentHead = $participant->deptHead ?? $employee?->department?->head;

        $recipients = collect([$employee]);

        if (in_array($type, ['h-30', 'h-14', 'h-7', 'h-1'])) {
            $recipients->push($departmentHead);
        }

        if (in_array($type, ['h-7', 'h-1'])) {
            $recipients->push($supervisor);
        }

        // Filter null & cegah duplikat ID
        $validRecipients = $recipients->filter()->unique('id');

        if ($validRecipients->isNotEmpty()) {
            Notification::send($validRecipients, new McuReminderNotification($participant, $type));
        }

        $participant->update(['notification_status' => $nextStatus]);
    }
}
