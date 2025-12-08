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
        $this->end_date   = $data['end'];

        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
    }
    #[On('chartManhoursUpdate')]
    public function loadData()
    {
        $dataManhours = Manhour::dateRange($this->start_date, $this->end_date);
        $dataManhours->selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereYear('date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $data = [
            'months' => $dataManhours->pluck('month')->map(function ($m) {
                return Carbon::create()->month($m)->format('M');
            })->toArray(),
            'msm' => $dataManhours->where('company', 'PT. MSM')->pluck('total')->toArray(),
            'ttn' => $dataManhours->where('company', 'PT. TTN')->pluck('total')->toArray()
        ];
        $this->data = json_encode($data);
        $this->dispatch('manhoursChart', $this->data);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
