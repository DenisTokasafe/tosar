<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldInspectionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspection:migrate-old-data';

    /**
     * The console command description.
     *
     * @var string
     */
   protected $description = 'Memetakan data fire_protections lama ke tabel inspection_sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pemetaan data lama...');

        // 1. Ambil semua kombinasi unik yang mendefinisikan satu kali jalan inspeksi
        $oldSessions = DB::table('fire_protections')
            ->select('inspection_date', 'inspected_by', 'area_name', 'area_photo_path')
            ->distinct()
            ->get();

        foreach ($oldSessions as $session) {
            // 2. Buat header di tabel inspection_sessions
            $sessionId = DB::table('inspection_sessions')->insertGetId([
                'inspection_date' => $session->inspection_date,
                'inspected_by'    => $session->inspected_by,
                'area_name'       => $session->area_name,
                'area_photo_path' => $session->area_photo_path,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 3. Hubungkan semua data detail yang punya atribut sama ke session ini
            DB::table('fire_protections')
                ->where('inspection_date', $session->inspection_date)
                ->where('inspected_by', $session->inspected_by)
                ->where('area_name', $session->area_name)
                ->update(['inspection_session_id' => $sessionId]);

            $this->info("Berhasil memetakan Sesi: {$session->area_name} tanggal {$session->inspection_date}");
        }

        $this->info('Migrasi data selesai!');
    }
}
