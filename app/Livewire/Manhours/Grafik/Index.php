<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Manhour;

class Index extends Component
{
    public $data,$dataManpower;
    public $start_date;
    public $end_date;

    public function mount()
    {
        // Default filter tanggal
        if (!$this->start_date || !$this->end_date) {
            $this->start_date = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->end_date = Carbon::now()->endOfYear()->format('Y-m-d');
        }
        $this->loadData();
    }
    #[On('dateRangeManhours')]
    public function updateDateRange($data)
    {
         if (!$data['start'] || !$data['end']) {
            $this->start_date = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->end_date = Carbon::now()->endOfYear()->format('Y-m-d');
        }else{
            $this->start_date = $data['start'];
            $this->end_date   = $data['end'];
        }
        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
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
        $msmData = Manhour::dateRange($this->start_date, $this->end_date)
            ->where('company', 'PT. MSM')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();

        // === PT. TTN ===
        $ttnData = Manhour::dateRange($this->start_date, $this->end_date)
            ->where('company', 'PT. TTN')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();
        // === CONTRACTOR ===
        $contractorData = Manhour::dateRange($this->start_date, $this->end_date)
            ->where('company_category', 'CONTRACTOR')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();

            // **** MANPOWER ****
        // === PT. MSM ===
        $msmDataManpower = Manhour::dateRange($this->start_date, $this->end_date)
            ->where('company', 'PT. MSM')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();

        // === PT. TTN ===
        $ttnDataManpower = Manhour::dateRange($this->start_date, $this->end_date)
            ->where('company', 'PT. TTN')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();
        // === CONTRACTOR ===
        $contractorDataManpower = Manhour::dateRange($this->start_date, $this->end_date)
            ->where('company_category', 'CONTRACTOR')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();

        // Format data: pastikan semua bulan ada (0 jika kosong)
        $msm = [];
        $ttn = [];
        $contractor = [];
        $msmManpower = [];
        $ttnManpower = [];
        $contractorManpower = [];

        foreach ($monthsRaw as $m) {
            $msm[]        = $msmData[$m] ?? 0;
            $ttn[]        = $ttnData[$m] ?? 0;
            $contractor[] = $contractorData[$m] ?? 0;
            $msmManpower[]        = $msmDataManpower[$m] ?? 0;
            $ttnManpower[]        = $ttnDataManpower[$m] ?? 0;
            $contractorManpower[] = $contractorDataManpower[$m] ?? 0;
        }

        // Data final untuk chart
        $this->data = json_encode([
            'months' => $months,
            'msm'    => $msm,
            'ttn'    => $ttn,
            'contractor'    => $contractor
        ]);
        $this->dispatch('manhoursChart', $this->data);
        $this->dataManpower = json_encode([
            'months' => $months,
            'msm'    => $msmManpower,
            'ttn'    => $ttnManpower,
            'contractor'    => $contractorManpower
        ]);
        $this->dispatch('manpowerChart', $this->dataManpower);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
