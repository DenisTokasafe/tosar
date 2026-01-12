<?php

namespace App\Livewire\Wpi;

use Livewire\Component;
use App\Models\WpiReport;
use App\Helpers\FileHelper;
use Livewire\WithPagination;

class WpiList extends Component
{
    use WithPagination;

    public $search = '';

    // Reset halaman saat user mengetik di kolom pencarian
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteReport($id)
    {
        // Ambil laporan beserta relasi findings-nya
        $report = WpiReport::with('findings')->find($id);

        if ($report) {
            // 1. Hapus file fisik di storage agar tidak memenuhi disk
            foreach ($report->findings as $finding) {
                // Hapus foto temuan
                if ($finding->photos) {
                    foreach ($finding->photos as $path) FileHelper::deleteFile($path);
                }
                // Hapus foto pencegahan
                if ($finding->photos_prevention) {
                    foreach ($finding->photos_prevention as $path) FileHelper::deleteFile($path);
                }
            }

            // 2. Hapus data dari database
            $report->delete();

            // 3. Kirim notifikasi sukses
            $this->dispatch('alert', [
                'text' => 'Laporan dan lampiran berhasil dihapus secara permanen.',
                'backgroundColor' => "linear-gradient(to right, #ef4444, #991b1b)",
            ]);
        }
    }

    public function render()
    {
        // Query dengan pencarian pada departemen atau lokasi
        $reports = WpiReport::query()->where('department', 'like', '%' . $this->search . '%')
            ->orderBy('report_date', 'desc')
            ->paginate(10);

        return view('livewire.wpi.wpi-list', [
            'reports' => $reports
        ]);
    }
}
