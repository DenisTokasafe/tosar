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
    public $years;
    public function mount()
    {
        $this->loadData();
    }
    #[On('dateRangeUpdated')]
     public function updateDateRange($data)
    {
        // Cek apakah data start dan end tersedia dan tidak kosong
        if (!empty($data['start']) && !empty($data['end'])) {
            $this->start_date = $data['start'];
            $this->end_date   = $data['end'];

            // Opsional: Jika menggunakan range, mungkin Anda ingin mereset filter tahun
            // agar tidak bentrok dengan filter tanggal spesifik
            $this->years = null;
        } else {
            // Kondisi jika filter tanggal dihapus (Kosong)
            $this->start_date = null;
            $this->end_date   = null;

            // Set tahun ke tahun dari bulan lalu
            $this->years = Carbon::now()->subMonth()->year;
        }

        $this->loadData();
    }

    public function loadData()
    {
        $dataHazard = ModelsHazard::query()
        // 1. Jika ada rentang tanggal (start & end), gunakan scope dateRange
        ->when($this->start_date && $this->end_date, function ($q) {
            return $q->dateRange($this->start_date, $this->end_date);
        })
        // 2. Jika rentang tanggal TIDAK ADA, gunakan filter tahun (fallback ke bulan lalu)
        ->when(!$this->start_date || !$this->end_date, function ($q) {
            return $q->whereYear('tanggal', $this->years);
        })->get();

        $dataHazard->selectRaw('MONTH(tanggal) as month, COUNT(*) as total')
            ->whereYear('tanggal',$this->years?? Carbon::now()->year)
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
