<?php

namespace App\Livewire\Dashboard\Hazard;

use Carbon\Carbon;
use App\Models\Hazard;
use Livewire\Component;
use Livewire\Attributes\On;

class HazardUserReport extends Component
{
    public $pelapor; // nama department atau contractor
    public $start_date;
    public $end_date;
    // Trigger awal saat komponen dimuat
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
    #[On('datePelaporUpdated')]
    public function loadData()
    {
        // Ambil semua hazard beserta relasi
        $year = Carbon::now()->year;
        $hazards = Hazard::with('pelapor')->when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        })->whereYear('tanggal', Carbon::now()->year)->get();

        // Kumpulkan kategori (nama department jika ada, kalau kosong pakai contractor)
        $grouped = $hazards->groupBy(function ($hazard) {
            if ($hazard->pelapor) {
                return $hazard->pelapor->name;
            } else {
                return 'Tidak Diketahui';
            }
        });
        // Hitung jumlah per kategori dan urutkan dari terbesar ke terkecil
        $counts = $grouped->map->count()->sortDesc() ->take(10);
        // Hitung jumlah per kategori
        $value = [
            'year' => $year,
            'label'  => $counts->keys()->values()->toArray(),   // urutan label mengikuti sortDesc()
            'counts' => $counts->values()->toArray(),            // urutan data sesuai label

        ];
        $this->pelapor = json_encode($value);
        $this->dispatch('distribusiPelapor', $this->pelapor);
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.dashboard.hazard.hazard-user-report');
    }
}
