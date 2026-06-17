<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Kirim notifikasi.
     *
     * @param  object  $notifiable (Model User/Entitas yang menerima)
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // Pastikan class notifikasi memiliki method 'toWhatsApp'
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        // Ambil data array (phone dan message) dari method toWhatsApp()
        $messageData = $notification->toWhatsApp($notifiable);

        $phone = $messageData['phone'] ?? null;
        $message = $messageData['message'] ?? null;

        // Jika nomor WA tidak diisi manual saat pembuatan jadwal, batalkan pengiriman
        if (empty($phone) || empty($message)) {
            Log::warning("Gagal mengirim WA: Nomor telepon tidak ditemukan untuk User ID {$notifiable->id}");
            return;
        }

        try {
            // Contoh implementasi HTTP Request ke API WhatsApp (Misal: Fonnte)
            // SESUAIKAN DENGAN DOKUMENTASI PROVIDER API ANDA
            $response = Http::withHeaders([
                'Authorization' => env('WHATSAPP_API_TOKEN'), // Ambil token dari file .env
            ])->post(env('WHATSAPP_API_URL'), [
                'target' => $phone, // Beberapa provider menggunakan key 'phone' atau 'number'
                'message' => $message,
                // 'delay' => '2' // Opsional jika API mendukung
            ]);

            // Jika API merespon error, catat di log laravel (storage/logs/laravel.log)
            if ($response->failed()) {
                Log::error('WhatsApp API Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            // Tangkap error jika server API mati / timeout
            Log::error('WhatsApp Request Exception: ' . $e->getMessage());
        }
    }
}
