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
        $this->info('Memulai pemetaan data berdasarkan relasi Equipment Master...');

        // 1. Ambil kombinasi unik untuk mendefinisikan sesi
        // Kita join ke equipment_masters untuk mendapatkan nama area yang akurat
        $oldSessions = DB::table('fire_protections')
            ->join('equipment_masters', 'fire_protections.equipment_master_id', '=', 'equipment_masters.id')
            ->select(
                'fire_protections.inspection_date',
                'fire_protections.inspected_by',
                'fire_protections.area_photo_path',
                'equipment_masters.area as area_name' // Ambil area dari master
            )
            ->distinct()
            ->get();

        foreach ($oldSessions as $session) {
            // 2. Buat header di tabel inspection_sessions
            $sessionId = DB::table('inspection_sessions')->insertGetId([
                'inspection_date' => $session->inspection_date,
                'inspected_by'    => $session->inspected_by,
                'area_name'       => $session->area_name, // Area dari master id
                'area_photo_path' => $session->area_photo_path,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 3. Update fire_protections yang sesuai dengan kriteria session ini
            // Kita hubungkan kembali berdasarkan tanggal, inspektur, dan area master-nya
            DB::table('fire_protections')
                ->join('equipment_masters', 'fire_protections.equipment_master_id', '=', 'equipment_masters.id')
                ->where('fire_protections.inspection_date', $session->inspection_date)
                ->where('fire_protections.inspected_by', $session->inspected_by)
                ->where('equipment_masters.area', $session->area_name)
                ->update(['fire_protections.inspection_session_id' => $sessionId]);

            $this->info("Berhasil memetakan Sesi Area: {$session->area_name}");
        }

        $this->info('Migrasi data selesai!');
    }
}
