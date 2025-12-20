<?php

namespace App\Livewire\Dashboard\Hazard;

use App\Models\Hazard;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class HazardDistribusiStatus extends Component
{
    public $statusChart;
    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->loadData();
    }
    #[On('dateRangeUpdated')]
    public function updateDateRange($start, $end)
    {
        $this->start_date = $start;
        $this->end_date   = $end;
        $this->loadData();
    }
    #[On('chartUpdated')]
    public function loadData()
    {
        $dataHazard = Hazard::when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        });
        $data = $dataHazard->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->orderBy('status')->get();

        $value = [
            'labels' => $data->pluck('status')->toArray(),
            'values' => $data->pluck('total')->toArray(),
        ];
        $this->statusChart = json_encode($value);
        $this->dispatch('distribusiStatus', $this->statusChart);
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.dashboard.hazard.hazard-distribusi-status');
    }
}
