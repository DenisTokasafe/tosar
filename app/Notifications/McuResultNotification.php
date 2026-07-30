<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\McuResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class McuResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $result;
    protected $recipientType;
    protected $channels;

    public function __construct(McuResult $result, string $recipientType, array $channels = ['mail', 'database', \App\Channels\WhatsAppChannel::class])
    {
        $this->result = $result;
        $this->recipientType = $recipientType;
        $this->channels = $channels;
    }

    /**
     * Tentukan channel pengiriman berdasarkan target penerima.
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    /**
     * Format email untuk masing-masing penerima.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // PENTING: Ambil ulang relasi yang hilang akibat serialisasi Queue
        $this->result->loadMissing(['participant.employee', 'participant.supervisor']);

        $employeeName = $this->result->participant->employee->name ?? 'Karyawan';
        $supervisorName = $this->result->participant->supervisor->name ?? 'Supervisor';
        $statusText = str_replace('_', ' ', strtoupper($this->result->status));

        if ($this->recipientType === 'employee') {
            return (new MailMessage)
                ->subject('Hasil Review Medical Check-Up (MCU) Anda')
                ->greeting('Halo, ' . $employeeName)
                ->line('Dokter telah selesai meninjau hasil pemeriksaan kesehatan (MCU) Anda.')
                ->line('**Status Kebugaran:** ' . $statusText)
                ->line($this->result->status === 'temporary_unfit' && $this->result->follow_up_date ? '• Jadwal MCU Follow Up Anda: ' . $this->result->follow_up_date->format('d M Y') : '')
                ->action('Lihat Detail Hasil', url('/dashboard/mcu'))
                ->line('Terima kasih, tetap jaga kesehatan dan keselamatan kerja Anda.');
        }

        // Template Email Untuk Dept Head / Supervisor
        return (new MailMessage)
            ->subject('Pemberitahuan Status MCU Anggota Tim: ' . $employeeName)
            ->greeting('Halo, ' . ($notifiable->name ?? 'Supervisor'))
            ->line('Pemberitahuan bahwa proses review medis untuk anggota tim Anda telah selesai dilakukan oleh dokter.')
            ->line('**Nama Karyawan:** ' . $employeeName)
            ->line('**Status Kebugaran Kerja:** ' . $statusText)
            ->action('Buka Menu Monitoring', url('/supervisor/mcu-monitoring'));
    }

    /**
     * Method custom untuk pengiriman pesan WhatsApp via WhatsAppChannel.
     */
    public function toWhatsApp(object $notifiable): array
    {
        // 1. PENTING: Tambahkan 'diseaseCategories' ke dalam loadMissing
        $this->result->loadMissing(['participant.employee', 'participant.supervisor', 'diseaseCategories']);

        $employeeName = $this->result->participant->employee->name ?? 'Karyawan';
        $statusText = str_replace('_', ' ', strtoupper($this->result->status));

        // 2. Ambil daftar penyakit dari tabel pivot dan gabungkan menjadi string teks
        $diseases = $this->result->diseaseCategories->pluck('name')->join(', ');

        if ($this->recipientType === 'employee') {
            $text = "*HASIL MEDICAL CHECK-UP (MCU)*\n\n";
            $text .= "Halo Bapak/Ibu {$employeeName},\n";
            $text .= "Hasil Medical Check Up (MCU) Anda telah kami terima.\n\n";
            $text .= "*Berdasarkan hasil pemeriksaan, status kesehatan Anda adalah:* {$statusText}\n";

            // 3a. Sisipkan info penyakit untuk Karyawan (jika ada temuan)
            if ($diseases) {
                $text .= "*Temuan Medis / Penyakit:* {$diseases}\n";
            }
            if ($this->result->status === 'temporary_unfit' && $this->result->follow_up_date) {
                $text .= "*Jadwal MCU Follow Up:* " . $this->result->follow_up_date->format('d M Y') . "\n";
            }

            $text .= "\nDetail selengkapnya dapat Anda lihat melalui Dashboard sistem.";

            $phone = $notifiable->whatsapp_number ?? $notifiable->phone ?? $this->result->participant->whatsapp_number;
        } else {
            $spvName = $notifiable->name ?? 'Bapak/Ibu Atasan';

            $text = "*PEMBERITAHUAN HASIL MCU ANGGOTA TIM*\n\n";
            $text .= "Halo {$spvName},\n";
            $text .= "Proses review medis untuk anggota tim Anda telah selesai dilakukan oleh dokter.\n\n";
            $text .= "*Nama Karyawan:* {$employeeName}\n";
            $text .= "*Status Kebugaran Kerja:* {$statusText}\n";

            if ($diseases) {
                $text .= "*Temuan Medis / Penyakit:* {$diseases}\n";
            }

            $phone = $notifiable->whatsapp_number ?? $notifiable->phone ?? $this->result->participant->spv_wa_number;
        }

        return [
            'phone'   => $phone,
            'message' => $text
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'mcu_result_id' => $this->result->id,
            'status'        => $this->result->status,
            'message'       => $this->recipientType === 'employee'
                ? 'Hasil review MCU Anda sudah keluar dengan status ' . $this->result->status
                : 'Anggota tim Anda (' . ($this->result->participant->employee->name ?? 'Karyawan') . ') telah di-review dengan status ' . $this->result->status,
        ];
    }
}
