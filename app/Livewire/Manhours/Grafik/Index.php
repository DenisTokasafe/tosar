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
    #[On('chartManhoursUpdate')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date   = $data['end'];

        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
    }
    #[On('chartTrandUpdated')]
    public function loadData()
    {
        $dataManhours = Manhour::when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        });
        $dataManhours->selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereYear('date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $data = [
            'months' => $dataManhours->pluck('month')->map(function ($m) {
                return Carbon::create()->month($m)->format('M');
            })->toArray(),
            'counts' => $dataManhours->where('company', 'PT. MSM')->pluck('total')->toArray()
        ];
        $this->data = json_encode($data);
        $this->dispatch('manhoursChart', $this->data);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
