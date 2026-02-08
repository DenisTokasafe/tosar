<?php

namespace App\Livewire\Inspection;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Location;
use App\Models\FireProtection;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithPagination;

class FireInspectionList extends Component
{
    use WithPagination;

    public $selectedItems = [];
    public $selectAll = false;

    public $type;
    public $date;
    public $area;
    public $search_type = '';
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
    public function updatingSearchType()
    {
        $this->resetPage();
    }
    public function updatingLocationId()
    {
        $this->resetPage();
    }
    public function updatingDate()
    {
        $this->resetPage();
    }
    public function clear_filter()
    {
        $this->reset('search_type', 'location_id', 'date', 'area', 'searchLocation');
    }
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
        if ($this->type === 'Fire Hydrant' && str_contains(strtolower($this->searchLocation), 'maesa camp')) {
            $this->fields['Fire Hydrant']['checks'] = ['Box', 'Hose', 'Rack', 'Valve', 'Nozel'];
        } else {
            // Kembalikan ke default jika bukan Maesa Camp
            $this->fields['Fire Hydrant']['checks'] = ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'];
        }
    }
    public function updatedType($value)
    {
        $this->type = $value;
        // Logika Khusus untuk Maesa Camp
        if ($value === 'Fire Hydrant' && str_contains(strtolower($this->searchLocation), 'maesa camp')) {
            $this->fields['Fire Hydrant']['checks'] = ['Box', 'Hose', 'Rack', 'Valve', 'Nozel'];
        } else {
            // Kembalikan ke default jika bukan Maesa Camp
            $this->fields['Fire Hydrant']['checks'] = ['Air', 'Kaca', 'Nozzle', 'Box', 'Hose', 'Kunci Hydrant'];
        }
        // Jika area sudah terpilih, refresh daftar alat di area tersebut
        if ($this->location_id) {
            $this->selectLocation($this->location_id, $this->searchLocation);
        }
    }
    // Tambahkan ini di dalam class FireInspectionList
    public function getInspectionsProperty()
    {
        return FireProtection::query()
            ->searchByType($this->search_type)
            ->searchByLocation($this->location_id)
            ->searchInstectionsByDate($this->date)
            ->get(); // Gunakan get() bukan paginate() untuk ambil semua ID yang terfilter
    }
    // Fungsi Logika Select All
    public function updatedSelectAll($value)
    {
        if ($value) {
            // Ambil semua ID dari hasil query inspeksi saat ini
            $this->selectedItems = $this->getInspectionsProperty()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    // Fungsi Hapus Masal
    public function deleteSelected()
    {
        if (empty($this->selectedItems)) return;

        // Proses hapus
        FireProtection::whereIn('id', $this->selectedItems)->delete();

        // Reset state
        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch(
            'alert',
            [
                'text' => "Data berhasil di hapus!!!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "linear-gradient(to right, #ff3333, #ff6666)",
            ]
        );
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
            'month' => Carbon::parse($this->date)->translatedFormat('F Y'),
            'tgl' => Carbon::parse($this->date)->translatedFormat('d, F Y'),
            'submitted_by' => $inspections->first()->submitted_by ?? 'N/A',
            'inspection_number' => $inspections->first()->inspection_number ?? 'N/A',
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
            'inspections' => FireProtection::with('equipmentMaster.location')
                ->searchByType($this->search_type)
                ->searchByLocation($this->location_id)
                ->searchInstectionsByDate($this->date)
                ->orderBy('inspection_date', 'desc')
                ->paginate(10),
        ]);
    }
    public function paginationView()
    {
        return 'paginate.pagination';
    }
}
