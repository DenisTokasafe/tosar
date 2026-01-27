<?php

namespace App\Livewire\Inspection;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\FireProtection;
use Barryvdh\DomPDF\Facade\Pdf;

class FireInspectionList extends Component
{
    public $fields = [
        'Fire Extinguisher' => [
            'inputs' => ['FE No', 'FE Type', 'Capacity'],
            'checks' => ['Nozzle', 'Hose', 'Pressure Indicator', 'Head Cap', 'Pin', 'Hook', 'Usage Guide', 'FE Sign']
        ],
        'Fire Hose Cabinet' => [
            'inputs' => ['Box No', 'Box'],
            'checks' => ['Hose', 'Rack', 'Nozzle', 'Valve']
        ],
        'Muster Point' => [
            'inputs' => ['ID Muster Point'],
            'checks' => ['Access', 'Visibility', 'Colour', 'Condition of Board', 'Condition of Pole', 'Letter'],
        ],
        'Fire Hydrant' => [
            'inputs' => ['Hydrant No'],
            'checks' => ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'],
        ],

        'Eye Wash & Safety Shower' => [
            'inputs' => ['E&S No'],
            'checks' => ['Access', 'Signage', 'Water Flow', 'Hose Condition', 'Nozzle Condition', 'Drainage'],
        ],
        'Fire Hose Reel' => [
            'inputs' => ['Hose Reel No'],
            'checks' => ['Hose', 'Reel', 'Nozzle', 'Valve', 'Air', 'Cover'],
        ],
        'Fire sprinkler system' => [
            'inputs' => ['Sprinkler No'],
            'checks' => ['Line Pipa', 'Main Valve', 'Drain Valve', 'Test valve', 'Alarm', 'Pressure', 'Access'],
        ],
        'Ring Buoy' => [
            'inputs' => ['Ring Buoy No'],
            'checks' => ['Ring Buoy', 'Access', 'Tempat Ring Buoy', 'Tali'],
        ],
    ];
    public function exportAllByMonth()
    {
        // 1. Ambil data berdasarkan type yang aktif dan bulan berjalan
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $inspections = FireProtection::where('type', $this->type) // Memfilter berdasarkan property $type yang dipilih
            ->whereMonth('inspection_date', $currentMonth)
            ->whereYear('inspection_date', $currentYear)
            ->orderBy('inspection_date', 'asc')
            ->get();

        // 2. Validasi jika data kosong
        if ($inspections->isEmpty()) {
            $this->dispatch('alert', [
                'text' => "Tidak ada data {$this->type} untuk periode " . Carbon::now()->translatedFormat('F Y'),
                'backgroundColor' => "background: linear-gradient(135deg, #f44336, #d32f2f);",
            ]);
            return;
        }

        // 3. Ambil struktur header dinamis dari $this->fields
        $structure = $this->fields[$this->type] ?? null;

        // 4. Generate PDF menggunakan template yang sudah ada
        $pdf = Pdf::loadView('pdf.dynamic-report', [
            'data' => $inspections, // Mengirim banyak data (Collection)
            'type' => $this->type,
            'structure' => $structure,
            'month' => Carbon::now()->translatedFormat('F Y'),
        ])->setPaper('a4', 'landscape');

        // 5. Nama file yang dinamis
        $filename = "Rekap_Inspeksi_" . str_replace(' ', '_', $this->type) . "_" . Carbon::now()->format('m_Y') . ".pdf";

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
    public function render()
    {
        return view('livewire.inspection.fire-inspection-list', [
            'inspections' => FireProtection::latest()->paginate(10)
        ]);
    }
}
