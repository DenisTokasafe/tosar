<?php

namespace App\Livewire\Dashboard\Hazard;

use Carbon\Carbon;
use App\Models\Hazard;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class HazardDistribusiStatus extends Component
{
    public $statusChart;
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
        $dataHazard = Hazard::with(['department', 'contractor']) // Sertakan eager loading jika perlu
            // 1. Jika ada rentang tanggal, gunakan scope dateRange
            ->when($this->start_date && $this->end_date, function ($q) {
                $q->dateRange($this->start_date, $this->end_date);
            })
            // 2. Jika rentang tanggal kosong, maka filter berdasarkan tahun
            ->when(!$this->start_date || !$this->end_date, function ($q) {
                $q->whereYear('tanggal', $this->years);
            })
            ->get();
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
