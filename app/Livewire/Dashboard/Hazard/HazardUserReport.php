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
    public $years;
    // Trigger awal saat komponen dimuat
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
        // Ambil semua hazard beserta relasi

        $hazards = Hazard::with('pelapor')->when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        })->when(!$this->start_date || !$this->end_date, function ($q) {
            $q->whereYear('tanggal', $this->years);
        })->get();

        // Kumpulkan kategori (nama department jika ada, kalau kosong pakai contractor)
        $grouped = $hazards->groupBy(function ($hazard) {
            if ($hazard->pelapor) {
                return $hazard->pelapor->name;
            } else {
                return 'Tidak Diketahui';
            }
        });
        // Hitung jumlah per kategori dan urutkan dari terbesar ke terkecil
        $counts = $grouped->map->count()->sortDesc()->take(10);
        // Hitung jumlah per kategori
        $value = [
            'year' => $this->years ?? Carbon::now()->year,
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
