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
        // 1. Ambil tanggal paling akhir (Data terbaru)
        $lastDateRaw = Hazard::max('tanggal');

        if ($lastDateRaw) {
            $lastDate = Carbon::parse($lastDateRaw);

            // 2. Set end_date ke tanggal terbaru
            $this->end_date = $lastDate->format('d-m-Y');

            // 3. Set start_date ke 12 bulan sebelumnya dari tanggal terbaru
            // Gunakan startOfMonth agar mencakup bulan penuh jika diinginkan
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('d-m-Y');
        } else {
            // Fallback jika database kosong
            $this->start_date = now()->subMonths(11)->startOfMonth()->format('d-m-Y');
            $this->end_date = now()->format('d-m-Y');
        }

        $this->loadData();
    }
    #[On('dateRangeUpdated')]
    public function updateDateRange($data)
    {
        // Cek apakah data start dan end tersedia dan tidak kosong
        if (!empty($data['start']) && !empty($data['end'])) {
            $this->start_date = $data['start'];
            $this->end_date = $data['end'];
            $this->years = null; // Reset filter tahun
        } else {
            // Kondisi jika filter tanggal dihapus oleh user
            $lastDateRaw = Hazard::max('tanggal');
            $lastDate = $lastDateRaw ? Carbon::parse($lastDateRaw) : Carbon::now();

            // Kembalikan ke 12 bulan terakhir
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('d-m-Y');
            $this->end_date = $lastDate->format('d-m-Y');

            // Opsional: Karena Anda menggunakan range 12 bulan, filter single year mungkin tidak relevan lagi
            $this->years = null;
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
