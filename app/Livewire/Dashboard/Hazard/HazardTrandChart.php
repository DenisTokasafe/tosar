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
    // 1. Inisialisasi Base Query (Kriteria Filter Utama)
    $baseQuery = ModelsHazard::query()
        ->when($this->start_date && $this->end_date, function ($q) {
            return $q->dateRange($this->start_date, $this->end_date);
        })
        ->when(!$this->start_date || !$this->end_date, function ($q) {
            // Gunakan years dari properti, fallback ke tahun dari bulan lalu jika null
            $yearFilter = $this->years ?? now()->subMonth()->year;
            return $q->whereYear('tanggal', $yearFilter);
        });

    // 2. Ambil Data Statistik untuk Grafik (Gunakan clone agar baseQuery tidak rusak)
    $chartStats = (clone $baseQuery)
        ->selectRaw('MONTH(tanggal) as month, COUNT(*) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // 3. Format data untuk Chart (Jan, Feb, Mar, dst)
    $data = [
        'months' => $chartStats->pluck('month')->map(function ($m) {
            return Carbon::create()->month($m)->format('M');
        })->toArray(),
        'counts' => $chartStats->pluck('total')->toArray()
    ];

    // 4. Simpan ke property dan Dispatch
    $this->data = json_encode($data);
    $this->dispatch('trandChart', $this->data);

    // Jika Anda butuh list datanya untuk tabel di view yang sama:
    // $this->hazards = $baseQuery->latest('tanggal')->get();
}

public function render()
{
    // Opsional: Jika loadData berat, pertimbangkan memanggilnya hanya saat filter berubah
    return view('livewire.dashboard.hazard.hazard-trand-chart');
}
