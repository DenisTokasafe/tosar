<?php

namespace App\Livewire\Wpi;

use Livewire\Component;
use App\Models\WpiReport;
use App\Models\Contractor;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $report = WpiReport::with('findings')->find($id);

        if ($report) {
            // Loop setiap temuan untuk menghapus foto fisik
            foreach ($report->findings as $finding) {
                // Hapus foto temuan utama
                if ($finding->photos) {
                    foreach ($finding->photos as $path) {
                        \App\Helpers\FileHelper::deleteFile($path);
                    }
                }
                // Hapus foto tindakan pencegahan
                if ($finding->photos_prevention) {
                    foreach ($finding->photos_prevention as $path) {
                        \App\Helpers\FileHelper::deleteFile($path);
                    }
                }
            }

            // Hapus data laporan (cascade delete ke findings jika diatur di migrasi)
            $report->delete();

            $this->dispatch('alert', [
                'text' => 'Laporan berhasil dihapus secara permanen.',
                'backgroundColor' => "linear-gradient(to right, #ef4444, #991b1b)",
            ]);
        }
    }
    public function exportPDF($id)
    {
        $report = WpiReport::with(['findings'])->findOrFail($id);
        $isContractor = Contractor::where('contractor_name', $report->department)->exists();
        $deptLabel = $isContractor ? 'Contractor' : 'Department';
        $pdf = Pdf::loadView('pdf.wpi-report', compact('report', 'deptLabel'))
            ->setOption(['isPhpEnabled' => true])
            ->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Laporan_WPI_" . $report->report_date . ".pdf");
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
