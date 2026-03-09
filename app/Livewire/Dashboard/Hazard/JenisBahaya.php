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
        // Query dengan filter tanggal/tahun dan filter khusus 'OHS Hazard Report'
        $rawData = Hazard::query()
            ->whereHas('eventType', function ($q) {
                $q->where('event_type_name', 'OHS Hazard Report');
            })
            ->when($this->start_date && $this->end_date, function ($q) {
                $q->dateRange($this->start_date, $this->end_date);
            })
            ->when(!$this->start_date || !$this->end_date, function ($q) {
                $q->whereYear('tanggal', $this->years);
            })
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

        // Ambil list unik untuk Sumbu X (Bulan) dan Legend (Jenis Bahaya)
        $months = $rawData->pluck('bulan_tahun')->unique()->values()->toArray();
        $jenisLabels = $rawData->map(fn($item) => $item->eventSubType->event_sub_type_name ?? 'N/A')->unique()->values();

        $series = [];
        foreach ($jenisLabels as $jenis) {
            $dataPoint = [];
            foreach ($months as $month) {
                $match = $rawData->first(
                    fn($item) =>
                    $item->bulan_tahun === $month &&
                        ($item->eventSubType->event_sub_type_name ?? 'N/A') === $jenis
                );
                $dataPoint[] = $match ? $match->total : 0;
            }

            $series[] = [
                'name' => $jenis,
                'type' => 'bar',
                'stack' => 'total', // Bar bertumpuk
                'emphasis' => ['focus' => 'series'],
                'data' => $dataPoint
            ];
        }

        $this->chartJenisBahaya = json_encode([
            'labels' => $months, // Sumbu X (Bulan)
            'series' => $series, // Data per Jenis Bahaya
            'legend' => $jenisLabels->toArray(),
            'range'  => ($this->start_date && $this->end_date) ? "$this->start_date s/d $this->end_date" : null
        ]);

        // Kirim data ke Frontend
        $this->dispatch('updateJenisBahayaChart', $this->chartJenisBahaya);
    }

    public function render()
    {
        return view('livewire.dashboard.hazard.jenis-bahaya');
    }
}
