<?php

namespace App\Livewire\Dashboard\Hazard;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Hazard as ModelsHazard;

class HazardTrandChart extends Component
{
    public $data;
    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->loadData();
    }
    #[On('dateRangeUpdated')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date   = $data['end'];

        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
    }

    public function loadData()
    {
        $year = Carbon::now()->subMonth()->year;
        $dataHazard = ModelsHazard::when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        });
        $dataHazard->selectRaw('MONTH(tanggal) as month, COUNT(*) as total')
            ->whereYear('tanggal', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $data = [
            'months' => $dataHazard->pluck('month')->map(function ($m) {
                return Carbon::create()->month($m)->format('M');
            })->toArray(),
            'counts' => $dataHazard->pluck('total')->toArray()
        ];
        $this->data = json_encode($data);
        $this->dispatch('trandChart', $this->data);
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.dashboard.hazard.hazard-trand-chart');
    }
}
