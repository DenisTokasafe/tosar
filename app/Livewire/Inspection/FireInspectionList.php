<?php

namespace App\Livewire\Inspection;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Location;
use App\Models\FireProtection;
use Barryvdh\DomPDF\Facade\Pdf;

class FireInspectionList extends Component
{
    public $type;
    public $date;
    public $area;
    public $location_id;
    public $show_location = false;
    public $locations = [];
    public $searchLocation = '';
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

        'Eyewash & Safety Shower' => [
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
    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(50)
                ->get();
            $this->show_location = true;
        } else {
            $this->locations = [];
            $this->show_location = false;
        }
    }
    public function selectLocation($id, $name)
    {
        $this->location_id = $id;
        $this->searchLocation = $name;
        $this->area = $name;
        $this->show_location = false;
         if ($this->type === 'Fire Hydrant' && str_contains(strtolower($name), 'maesa camp')) {
            $this->fields['Fire Hydrant']['checks'] = ['Box', 'Hose', 'Rack', 'Valve', 'Nozel'];
        } else {
            // Kembalikan ke default jika bukan Maesa Camp
            $this->fields['Fire Hydrant']['checks'] = ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'];
        }
    }
    public function exportPDF()
    {
        // Gunakan Carbon untuk mengambil angka Bulan dan Tahun saja
        $date = Carbon::parse($this->date);
        $currentMonth = $date->month; // Menghasilkan angka 1-12
        $currentYear  = $date->year;  // Menghasilkan angka 4 digit (misal: 2024)

        $inspections = FireProtection::whereHas('equipmentMaster', function ($query) {
            $query->where('type', $this->type)
                ->where('location_id', $this->location_id);
        })
            ->whereMonth('inspection_date', $currentMonth)
            ->whereYear('inspection_date', $currentYear)
            ->orderBy('inspection_date', 'asc')
            ->with('equipmentMaster') // Eager load untuk performa saat looping di PDF
            ->get();

        if ($inspections->isEmpty()) {
            $this->dispatch('alert', [
                'text' => "Tidak ada data {$this->type} untuk periode " . Carbon::parse($this->date)->translatedFormat('F Y'),
                'backgroundColor' => "background: linear-gradient(135deg, #f44336, #d32f2f);",
            ]);
            return;
        }

        $structure = $this->fields[$this->type] ?? null;

        // 1. Load View
        $pdf = Pdf::loadView('pdf.dynamic-report', [
            'data' => $inspections,
            'type' => $this->type,
            'area' => $inspections->first()->equipmentMaster->location->name ?? 'N/A',
            'structure' => $structure,
            'month' => Carbon::now()->translatedFormat('F Y'),
        ])->setPaper('a4', 'landscape');

        // 2. Render PDF terlebih dahulu agar bisa mengakses Canvas
        $pdf->render();

        // 3. Ambil Canvas untuk menambahkan penomoran halaman
        $canvas = $pdf->getCanvas();
        $font = null; // Ini akan otomatis menggunakan font default PDF (Helvetica/Times-Roman)
        $size = 9;

        /**
         * Parameter page_text:
         * (X, Y, Text, Font, Size, Color)
         * Untuk Landscape A4: X = 730 (Kanan), Y = 560 (Bawah)
         */
        $canvas->page_text(730, 560, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, $size, [0, 0, 0]);

        $filename = "Rekap_Inspeksi_" . str_replace(' ', '_', $this->type) . "_" . Carbon::now()->format('m_Y') . ".pdf";

        // 4. Download menggunakan output yang sudah dimodifikasi canvasnya
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
