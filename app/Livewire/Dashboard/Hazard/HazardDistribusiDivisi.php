<?php

namespace App\Livewire\Dashboard\Hazard;

use Carbon\Carbon;
use App\Models\Hazard;
use Livewire\Component;
use Livewire\Attributes\On;

class HazardDistribusiDivisi extends Component
{
    public $categories; // nama department atau contractor
    public $start_date;
    public $end_date;
    public $years;
    // Trigger awal saat komponen dimuat
    public function mount()
    {
        $firstDateRaw = Hazard::min('tanggal');
        $firstDate = $firstDateRaw ? Carbon::parse($firstDateRaw)->format('d-m-Y') : null;
        // Ambil tanggal paling akhir
        $lastDateRaw = Hazard::max('tanggal');
        $lastDate = $lastDateRaw ? Carbon::parse($lastDateRaw)->format('d-m-Y') : null;
        $this->start_date =  $firstDate;
        $this->end_date   =  $lastDate;
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
        $hazards = Hazard::with(['department', 'contractor'])
            // 1. Jika ada filter Range Tanggal, gunakan dateRange
            ->when($this->start_date && $this->end_date, function ($q) {
                $q->dateRange($this->start_date, $this->end_date);
            })
            // 2. Jika TIDAK ADA filter Range Tanggal, maka gunakan filter Tahun
            ->when(!$this->start_date || !$this->end_date, function ($q) {
                $q->whereYear('tanggal', $this->years);
            })
            ->get();

        // Kumpulkan kategori (nama department jika ada, kalau kosong pakai contractor)
        $grouped = $hazards->groupBy(function ($hazard) {
            if ($hazard->department) {
                return $hazard->department->department_name;
            } elseif ($hazard->contractor) {
                return $hazard->contractor->contractor_name;
            } else {
                return 'Tidak Diketahui';
            }
        });
        // Hitung jumlah per kategori dan urutkan dari terbesar ke terkecil
        $counts = $grouped->map->count()->sortDesc();
        // Hitung jumlah per kategori
        $value = [
            'year' => $this->years ?? Carbon::now()->year,
            'label'  => $counts->keys()->values()->toArray(),   // urutan label mengikuti sortDesc()
            'counts' => $counts->values()->toArray(),            // urutan data sesuai label

        ];
        $this->categories = json_encode($value);
        $this->dispatch('distribusiDivisi', $this->categories);
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.dashboard.hazard.hazard-distribusi-divisi');
    }
}
