<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Manhour;

class Index extends Component
{
    // Properti untuk menyimpan data yang dikirim ke ECharts
    public $combinedChartData = '[]';
    public $start_date = null;
    public $end_date = null;

    public function mount()
    {
        // Default filter tanggal
        if (!$this->start_date || !$this->end_date) {
            $this->start_date = Carbon::now()->startOfYear()->format('Y/m/d');
            $this->end_date = Carbon::now()->endOfYear()->format('Y/m/d');
        }
        $this->loadData();
    }

    #[On('dateRangeManhours')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date = $data['end'];
        $this->loadData();
    }

    #[On('chartManhoursUpdated')]
    public function loadData()
    {
        $dataHazard = Manhour::when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        });
        $dataHazard->selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereYear('date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $data = [
            'months' => $dataHazard->pluck('month')->map(function ($m) {
                return Carbon::create()->month($m)->format('M');
            })->toArray(),
            'msm' => $dataHazard->where('company', 'PT. MSM')->pluck('total')->toArray(),
            'ttn' => $dataHazard->where('company', 'PT. TTN')->pluck('total')->toArray(),
            'ttn' => $dataHazard->where('company_category', 'CONTRACTOR')->pluck('total')->toArray(),
        ];
        $this->combinedChartData = json_encode($data);
        $this->dispatch('updateCombinedChart', $this->combinedChartData);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
