<?php

namespace App\Livewire\Mcu;

use App\Models\McuParticipant;
use App\Models\McuResult;
use Carbon\Carbon;
use Livewire\Component;

class McuDashboard extends Component
{
    public function render()
    {
        // Gunakan tanggal hari ini sebagai patokan
        $today = Carbon::now()->toDateString();

        // 1. DATA KEHADIRAN MCU
        // Sudah mengikuti MCU (memiliki data di tabel mcu_results)
        $sudahMcu = McuParticipant::has('result')->count();

        // Belum/Tidak mengikuti MCU (TIDAK punya result DAN jadwal sudah lewat dari hari ini)
        $terlewatMcu = McuParticipant::whereDoesntHave('result')
            ->whereHas('schedule', function ($query) use ($today) {
                $query->whereDate('schedule_date', '<', $today);
            })->count();

        // Menunggu Jadwal (TIDAK punya result, tapi jadwalnya hari ini atau di masa depan)
        $menungguJadwal = McuParticipant::whereDoesntHave('result')
            ->whereHas('schedule', function ($query) use ($today) {
                $query->whereDate('schedule_date', '>=', $today);
            })->count();

        // 2. DATA STATUS KEBUGARAN (Fit Status)
        $fitStatusRaw = McuResult::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $fitStatus = [
            'fit_to_work'     => $fitStatusRaw['fit_to_work'] ?? 0,
            'fit_with_notes'  => $fitStatusRaw['fit_with_notes'] ?? 0,
            'temporary_unfit' => $fitStatusRaw['temporary_unfit'] ?? 0,
            'unfit'           => $fitStatusRaw['unfit'] ?? 0,
        ];

        // 3. DATA WORKFLOW STATUS
        $workflowStatusRaw = McuResult::selectRaw('workflow_status, count(*) as total')
            ->groupBy('workflow_status')
            ->pluck('total', 'workflow_status')
            ->toArray();

        $workflowStatus = [
            'pending_doctor' => $workflowStatusRaw['pending_doctor'] ?? 0,
            'reviewed'       => $workflowStatusRaw['reviewed'] ?? 0,
        ];
        return view('livewire.mcu.mcu-dashboard', compact(
            'sudahMcu',
            'terlewatMcu',
            'menungguJadwal',
            'fitStatus',
            'workflowStatus'
        ));
    }
}
