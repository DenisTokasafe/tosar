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
        // 1. Query utama dengan filter OHS Hazard Report yang ketat
        $query = Hazard::query()
            ->whereHas('eventType', function ($q) {
                $q->where('event_type_name', 'OHS Hazard Report');
            })
            ->when($this->start_date && $this->end_date, function ($q) {
                $q->dateRange($this->start_date, $this->end_date);
            })
            ->when(!$this->start_date || !$this->end_date, function ($q) {
                $q->whereYear('tanggal', $this->years);
            });

        // 2. Ambil raw data untuk chart
        $rawData = (clone $query)
            ->with(['eventSubType'])
            ->select(
                DB::raw("DATE_FORMAT(tanggal, '%b %Y') as bulan_tahun"),
                'event_sub_type_id',
                DB::raw('count(*) as total'),
                DB::raw("LAST_DAY(tanggal) as urutan_bulan")
            )
            ->groupBy('bulan_tahun', 'event_sub_type_id', 'urutan_bulan')
            ->orderBy('urutan_bulan')
            ->get();

        // 3. Ambil daftar bulan (X-Axis)
        $months = $rawData->pluck('bulan_tahun')->unique()->values()->toArray();

        // 4. Ambil daftar Jenis Bahaya UNIK yang hanya berelasi dengan OHS Hazard Report
        // Ini memastikan legend hanya berisi sub-type yang relevan
        $jenisLabels = $rawData->map(function ($item) {
            return $item->eventSubType->event_sub_type_name ?? 'N/A';
        })->unique()->values();

        // 5. Bangun Series Data untuk ECharts (Grouped Bar)
        $series = [];
        foreach ($jenisLabels as $jenis) {
            $dataPoint = [];
            foreach ($months as $month) {
                $match = $rawData->first(function ($item) use ($month, $jenis) {
                    return $item->bulan_tahun === $month &&
                        ($item->eventSubType->event_sub_type_name ?? 'N/A') === $jenis;
                });
                $dataPoint[] = $match ? $match->total : 0;
            }

            $series[] = [
                'name' => $jenis,
                'type' => 'bar',
                'barMaxWidth' => 20,
                'barGap' => '10%',
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
            'range'  => ($this->start_date && $this->end_date) ? "$this->start_date s/d $this->end_date" : "Tahun $this->years"
        ]);

        $this->dispatch('updateJenisBahayaChart', $this->chartJenisBahaya);
    }

    public function render()
    {
        return view('livewire.dashboard.hazard.jenis-bahaya');
    }
}
