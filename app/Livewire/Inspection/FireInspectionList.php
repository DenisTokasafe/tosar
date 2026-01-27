<?php

namespace App\Livewire\Inspection;

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
