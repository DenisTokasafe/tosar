<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Manhour;

class Index extends Component
{
    public $data, $manpowerData;
    public $start_date;
    public $end_date;
    public $years;
    public $filterSearch = '';


    public function mount()
    {
        // Default filter tanggal
        if (!$this->start_date || !$this->end_date) {
            $this->start_date = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->end_date = Carbon::now()->endOfYear()->format('Y-m-d');
        }
        $this->years = Carbon::now()->year;
        $this->loadData();
        $this->loadDataManpower();
    }
    #[On('manhoursSearchUpdated')]
    public function updateSearch($search)
    {
        $this->filterSearch = $search;
        $this->loadDataManpower();
        $this->loadData();
    }
    #[On('dateRangeManhours')]
    public function updateDateRange($data)
    {
        if (!$data['start'] || !$data['end']) {
            $this->start_date = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->end_date = Carbon::now()->endOfYear()->format('Y-m-d');
        } else {
            $this->start_date = $data['start'];
            $this->end_date   = $data['end'];
        }
        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
        $this->loadDataManpower();
    }
    #[On('chartManhoursUpdate')]
    public function loadData()
    {
        /// Ambil bulan unique berdasarkan tanggal dan rentang filter
        $monthsRaw = Manhour::dateRange($this->start_date, $this->end_date)
            ->selectRaw('DISTINCT MONTH(date) as month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();
        // Format bulan ke teks (Jan, Feb, Mar)
        $months = array_map(
            fn($m) =>
            Carbon::create()->month($m)->format('M'),
            $monthsRaw
        );
        // === PT. MSM ===
        $msmData = Manhour::dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company', 'PT. MSM')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();
        // === PT. TTN ===
        $ttnData = Manhour::dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company', 'PT. TTN')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();
        // === CONTRACTOR ===
        $contractorData = Manhour::dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company_category', 'CONTRACTOR')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();
        // Format data: pastikan semua bulan ada (0 jika kosong)
        $msm = [];
        $ttn = [];
        $contractor = [];
        foreach ($monthsRaw as $m) {
            $msm[]        = $msmData[$m] ?? 0;
            $ttn[]        = $ttnData[$m] ?? 0;
            $contractor[] = $contractorData[$m] ?? 0;
        }
        // Data final untuk chart
        $this->data = json_encode([
            'months' => $months,
            'msm'    => $msm,
            'ttn'    => $ttn,
            'contractor'    => $contractor
        ]);
        $this->dispatch('manhoursChart', $this->data);
    }
    #[On('chartManpowerUpdate')] // Ganti nama event agar tidak bentrok
    public function loadDataManpower()
    {

        /// Ambil bulan unique berdasarkan tanggal dan rentang filter
        $monthsRaw = Manhour::dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->selectRaw('DISTINCT MONTH(date) as month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();

        // Format bulan ke teks (Jan, Feb, Mar)
        $months = array_map(
            fn($m) => Carbon::create()->month($m)->format('M'),
            $monthsRaw
        );

        // === PT. MSM ===
        $msmData = Manhour::dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company', 'PT. MSM')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();

        // === PT. TTN ===
        $ttnData = Manhour::dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company', 'PT. TTN')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();

        // === CONTRACTOR ===
        $contractorData = Manhour::dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company_category', 'CONTRACTOR')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();

        // Format data: pastikan semua bulan ada (0 jika kosong)
        $msm_mp = []; // Menggunakan suffix _mp (manpower) untuk menghindari konflik nama
        $ttn_mp = [];
        $contractor_mp = [];

        foreach ($monthsRaw as $m) {
            $msm_mp[] = $msmData[$m] ?? 0;
            $ttn_mp[] = $ttnData[$m] ?? 0;
            $contractor_mp[] = $contractorData[$m] ?? 0;
        }
        // Data final untuk chart (Gunakan properti yang berbeda jika chart manpower dipisah)
        $data_manpower = [
            'months' => $months,
            'msm'    => $msm_mp,
            'ttn'    => $ttn_mp,
            'contractor' => $contractor_mp
        ];


        // Gunakan properti Livewire yang berbeda, misalnya $manpowerChartData
        $this->manpowerData = json_encode($data_manpower);
        // Dispatch event yang berbeda untuk grafik manpower
        $this->dispatch('manpowerChart', $this->manpowerData);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
