<?php

namespace App\Livewire\Dashboard\Hazard;

use App\Models\Hazard;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class JenisBahaya extends Component
{
    public $start_date;
    public $end_date;
    public $chartJenisBahaya;
    public $years;

    public function mount()
    {
        $lastDateRaw = Hazard::max('tanggal');
        if ($lastDateRaw) {
            $lastDate = Carbon::parse($lastDateRaw);
            $this->end_date = $lastDate->format('d-m-Y');
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('d-m-Y');
        } else {
            $this->end_date = now()->format('d-m-Y');
            $this->start_date = now()->subMonths(11)->startOfMonth()->format('d-m-Y');
        }

        $this->loadData();
    }

    #[On('dateRangeUpdated')]
    public function updatedDateRange($data)
    {
        if (!empty($data['start']) && !empty($data['end'])) {
            $this->start_date = $data['start'];
            $this->end_date   = $data['end'];
            $this->years      = null;
        } else {
            $lastDateRaw = Hazard::max('tanggal');
            $lastDate = $lastDateRaw ? Carbon::parse($lastDateRaw) : Carbon::now();
            $this->end_date   = $lastDate->format('d-m-Y');
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('d-m-Y');
            $this->years = null;
        }

        $this->loadData();
    }

    public function loadData()
    {
        // 1. Query utama - Menggunakan scopeByEventType melalui relasi eventSubType
        // Ini memastikan kita hanya mengambil data Hazard yang sub-tipenya milik 'OHS Hazard Report'
        $query = Hazard::query()
            ->whereHas('eventSubType', function ($q) {
                $q->byEventType('OHS Hazard Report');
            })
            ->when($this->start_date && $this->end_date, function ($q) {
                $q->dateRange($this->start_date, $this->end_date);
            })
            ->when(!$this->start_date || !$this->end_date, function ($q) {
                $q->whereYear('tanggal', $this->years);
            });

        // 2. Ambil raw data untuk chart
        $rawData = (clone $query)
            ->with(['eventSubType']) // Scope byEventType di model EventSubType akan otomatis menyaring record yang statusnya aktif (EnabledEventSubTypeScope)
            ->select(
                DB::raw("DATE_FORMAT(tanggal, '%b %Y') as bulan_tahun"),
                'event_sub_type_id',
                DB::raw('count(*) as total'),
                DB::raw("LAST_DAY(tanggal) as urutan_bulan")
            )
            ->groupBy('bulan_tahun', 'event_sub_type_id', 'urutan_bulan')
            ->orderBy('urutan_bulan')
            ->get();

        // 3. Ambil daftar bulan unik untuk Label X-Axis
        $months = $rawData->pluck('bulan_tahun')->unique()->values()->toArray();

        // 4. Ambil daftar Jenis Bahaya UNIK menggunakan scope untuk legend
        // Ini memastikan legend konsisten dengan data yang difilter
        $jenisLabels = $rawData->map(function ($item) {
            return $item->eventSubType->event_sub_type_name ?? 'N/A';
        })->unique()->values();

        // 5. Bangun Series Data untuk ECharts (Grouped Bar + Data Labels)
        $series = [];
        foreach ($jenisLabels as $jenis) {
            $dataPoint = [];
            foreach ($months as $month) {
                $match = $rawData->first(function ($item) use ($month, $jenis) {
                    return $item->bulan_tahun === $month &&
                        ($item->eventSubType->event_sub_type_name ?? 'N/A') === $jenis;
                });
                $dataPoint[] = $match ? (int)$match->total : 0;
            }

            $series[] = [
                'name' => $jenis,
                'type' => 'bar',
                'barMaxWidth' => 20,
                'barGap' => '15%',
                'barCategoryGap' => '35%',
                'label' => [
                    'show' => true,
                    'position' => 'top',
                    'distance' => 5,
                    'fontSize' => 10,
                    'formatter' => '{text|{c}}', // Syntax ECharts untuk custom label
                    'rich' => [
                        'text' => [
                            'align' => 'center'
                        ]
                    ],
                    // Logika agar angka 0 tidak tampil di JS formatter tetap disarankan,
                    // namun di sini kita siapkan strukturnya.
                ],
                'itemStyle' => [
                    'borderRadius' => [3, 3, 0, 0]
                ],
                'data' => $dataPoint,
            ];
        }

        $this->chartJenisBahaya = json_encode([
            'labels' => $months,
            'series' => $series,
            'legend' => $jenisLabels->toArray(),
            'range'  => ($this->start_date && $this->end_date)
                ? "Periode: " . \Carbon\Carbon::parse($this->start_date)->format('d M Y') . " - " . \Carbon\Carbon::parse($this->end_date)->format('d M Y')
                : "Tahun $this->years"
        ]);

        $this->dispatch('updateJenisBahayaChart', $this->chartJenisBahaya);
    }

    public function render()
    {
        return view('livewire.dashboard.hazard.jenis-bahaya');
    }
}
