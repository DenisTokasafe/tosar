<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Manhour;

class Index extends Component
{
    public $data;
    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->loadData();
    }
    #[On('dateRangeManhours')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date   = $data['end'];
        dd($this->start_date,$this->end_date);
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

        // Format data: pastikan semua bulan ada (0 jika kosong)
        $msm = [];
        $ttn = [];
        $contractor = [];

        for ($m = 1; $m <= 12; $m++) {
            $msm[] = $msmData[$m] ?? 0;
            $ttn[] = $ttnData[$m] ?? 0;
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

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
