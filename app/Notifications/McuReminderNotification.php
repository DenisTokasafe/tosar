<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\McuParticipant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class McuReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public McuParticipant $participant,
        public string $type // 'h-30', 'h-14', 'h-7', 'h-1', 'missed'
    ) {}

    // Tentukan channel berdasarkan target (Email, custom WhatsApp)
    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $date = $this->participant->schedule->schedule_date->format('d M Y');

        $message = (new MailMessage)
            ->subject("Pengingat Jadwal Medical Check-Up (MCU)")
            ->greeting("Halo {$notifiable->name},")
            ->line("Ini adalah pengingat bahwa Anda memiliki jadwal MCU pada tanggal **{$date}**.");

        if ($this->type === 'missed') {
            $message->subject("Pemberitahuan: Tidak Hadir MCU")
                ->line("Sistem mencatat Anda tidak hadir pada jadwal MCU yang telah ditentukan.");
        }

        return $message->action('Lihat Detail', url('/mcu/detail/' . $this->participant->id));
    }

    // Method custom untuk WhatsApp (Asumsi menggunakan API Provider seperti Fonnte/Watzap)
    public function toWhatsApp(object $notifiable): array
    {
        $date = $this->participant->schedule->schedule_date->format('d M Y');
        $employeeName = $this->participant->employee->name ?? 'Karyawan';

        // 1. JIKA NOTIFIKASI UNTUK SUPERVISOR
        if ($this->type === 'new_schedule_spv') {
            // Handle nama SPV jika manual (tidak ada object notifiable->name)
            $spvName = $notifiable->name ?? 'Bapak/Ibu Supervisor';

            $text = "*PEMBERITAHUAN JADWAL MCU BAWAHAN*\n\n";
            $text .= "Halo {$spvName},\n";
            $text .= "Anggota tim Anda, *{$employeeName}*, telah dijadwalkan untuk Medical Check-Up pada tanggal *{$date}*.\n";
            $text .= "Mohon bantuan Anda untuk memastikan kehadiran yang bersangkutan.";

            // Langsung ambil dari kolom database yang baru dibuat
            $phone = $this->participant->spv_wa_number;
        }
        // 2. JIKA NOTIFIKASI UNTUK KARYAWAN
        else {
            $text = "*PEMBERITAHUAN JADWAL MCU*\n\n";
            $text .= "Halo {$notifiable->name},\n";
            $text .= "Anda telah dijadwalkan untuk Medical Check-Up pada tanggal *{$date}*.\n";
            $text .= "Mohon persiapkan diri Anda.";

            // Langsung ambil dari kolom database karyawan
            $phone = $this->participant->whatsapp_number;
        }

        return [
            'phone' => $phone,
            'message' => $text
        ];
    }
}
