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
        $this->info('Memulai pemetaan data berdasarkan location_id...');

        // 1. Ambil kombinasi unik dari data lama
        // Kita join ke equipment_masters untuk mendapatkan location_id yang menjadi 'Induk Area'
        $oldSessions = DB::table('fire_protections')
            ->join('equipment_masters', 'fire_protections.equipment_master_id', '=', 'equipment_masters.id')
            ->select(
                'fire_protections.inspection_date',
                'fire_protections.inspected_by',
                'fire_protections.area_photo_path',
                'equipment_masters.location_id' // Ini yang menjadi acuan area sekarang
            )
            ->distinct()
            ->get();

        foreach ($oldSessions as $session) {
            // 2. Buat header di tabel inspection_sessions
            // Kita simpan location_id agar nanti mudah relasi ke tabel locations (jika ada)
            $sessionId = DB::table('inspection_sessions')->insertGetId([
                'inspection_date' => $session->inspection_date,
                'inspected_by'    => $session->inspected_by,
                'area_name'       => "Location ID: " . $session->location_id, // Sementara simpan ID-nya
                'area_photo_path' => $session->area_photo_path,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 3. Update fire_protections agar terhubung ke sesi ini
            // Kita filter detail alat yang punya location_id yang sama dalam tanggal yang sama
            DB::table('fire_protections')
                ->join('equipment_masters', 'fire_protections.equipment_master_id', '=', 'equipment_masters.id')
                ->where('fire_protections.inspection_date', $session->inspection_date)
                ->where('fire_protections.inspected_by', $session->inspected_by)
                ->where('equipment_masters.location_id', $session->location_id)
                ->update(['fire_protections.inspection_session_id' => $sessionId]);

            $this->info("Berhasil memetakan Sesi untuk Location ID: {$session->location_id}");
        }

        $this->info('Migrasi data selesai! Semua data lama kini terorganisir per Sesi.');
    }
}
