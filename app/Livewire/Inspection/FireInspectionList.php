<?php

namespace App\Livewire\Inspection;

use Livewire\Component;
use App\Models\FireProtection;
use Barryvdh\DomPDF\Facade\Pdf;

class FireInspectionList extends Component
{
    public function exportPDF($id)
    {
        // 1. Ambil data spesifik berdasarkan ID
        $inspection = FireProtection::findOrFail($id);

        // 2. Ambil struktur field berdasarkan type data tersebut
        // Pastikan property $this->fields bisa diakses (public atau didefinisikan di sini)
        $structure = $this->fields[$inspection->type] ?? null;

        if (!$structure) {
            $this->dispatch('alert', ['text' => "Struktur kolom untuk tipe ini tidak ditemukan!"]);
            return;
        }

        // 3. Load view PDF (kita bungkus dalam array agar template dinamis tetap bekerja)
        $pdf = Pdf::loadView('pdf.dynamic-report', [
            'data' => [$inspection], // Kirim sebagai array berisi 1 data agar loop di blade tidak error
            'type' => $inspection->type,
            'structure' => $structure,
            'month' => \Carbon\Carbon::parse($inspection->inspection_date)->format('F Y'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "Inspection_" . str_replace(' ', '_', $inspection->type) . "_" . $inspection->id . ".pdf");
    }
    public function render()
    {
        return view('livewire.inspection.fire-inspection-list', [
            'inspections' => FireProtection::latest()->paginate(10)
        ]);
    }
}
