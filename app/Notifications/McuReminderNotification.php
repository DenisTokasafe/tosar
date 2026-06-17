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
        // Jika input manual (AnonymousNotifiable), hanya kirim via WA karena tidak ada email
        if ($notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable) {
            return [WhatsAppChannel::class];
        }

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

        // Deteksi apakah yang menerima ini Atasan atau Karyawan
        $isManualSpv = $notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable;
        $isManager = $this->type === 'new_schedule_spv' || $isManualSpv || (isset($notifiable->id) && $notifiable->id !== $this->participant->employee_id);

        if ($isManager) {
            $spvName = $notifiable->name ?? 'Bapak/Ibu Atasan';
            $text = "*PEMBERITAHUAN JADWAL MCU BAWAHAN*\n\n";
            $text .= "Halo {$spvName},\n";
            $text .= "Anggota tim Anda, *{$employeeName}*, dijadwalkan untuk Medical Check-Up pada tanggal *{$date}*.\n";
            $text .= "Mohon bantuan Anda untuk memastikan kehadirannya.";

            // Jika SPV manual ambil dari spv_wa_number, jika SPV sistem ambil dari data model user-nya (pastikan ada field no_wa/phone)
            $phone = $isManualSpv || $this->type === 'new_schedule_spv'
                ? $this->participant->spv_wa_number
                : ($notifiable->whatsapp_number ?? $notifiable->phone ?? $this->participant->spv_wa_number);
        } else {
            $text = "*PEMBERITAHUAN JADWAL MCU*\n\n";
            $text .= "Halo {$notifiable->name},\n";
            $text .= "Anda telah dijadwalkan untuk Medical Check-Up pada tanggal *{$date}*.\n";
            $text .= "Mohon persiapkan diri Anda.";

            $phone = $this->participant->whatsapp_number;
        }

        return [
            'phone' => $phone,
            'message' => $text
        ];
    }
}
