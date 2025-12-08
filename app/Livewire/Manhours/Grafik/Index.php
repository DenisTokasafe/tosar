<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use App\Models\Manhour;
use Livewire\Component;
use Livewire\Attributes\On;

class Index extends Component
{
  // Properti untuk menyimpan data yang dikirim ke ECharts
    public $contractorData = '[]';
    public $msmTTNData = '[]';
    public $start_date = null;
    public $end_date = null;

    public function mount()
    {
        // Tetapkan filter tanggal default (misalnya, untuk tahun ini)
        if (!$this->start_date || !$this->end_date) {
            $this->start_date = Carbon::now()->startOfYear()->toDateString();
            $this->end_date = Carbon::now()->endOfYear()->toDateString();
        }
        $this->loadData();
    }

    #[On('dateRangeManhours')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date = $data['end'];
        $this->loadData();
    }

    #[On('chartTrandUpdated')]
    public function loadData()
    {
        // Panggil fungsi untuk mengambil data pertama
        $this->loadContractorData();

        // Panggil fungsi untuk mengambil data kedua
        $this->loadMSMTTNData();
    }

    /**
     * Mengambil Manhours per Bulan untuk SEMUA 'CONTRACTOR' (Gambar Pertama)
     */
    private function loadContractorData()
    {
        $query = Manhour::query()
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours') // SUM(manhours) BUKAN COUNT(*)
            ->dateRange($this->start_date, $this->end_date)
            ->where('company_category', 'CONTRACTOR') // Filter utama
            ->groupBy('month')
            ->orderBy('month');

        $dataManhours = $query->get();

        // Siapkan array data 12 bulan (dengan 0 jika kosong)
        $monthlyTotals = array_fill(1, 12, 0);
        foreach ($dataManhours as $item) {
            $monthlyTotals[$item->month] = (int)$item->total_manhours;
        }

        $data = [
            'months' => array_map(fn($m) => Carbon::create()->month($m)->format('M'), range(1, 12)),
            'manhours' => array_values($monthlyTotals)
        ];

        $this->contractorData = json_encode($data);
        $this->dispatch('updateContractorChart', $this->contractorData);
    }

    /**
     * Mengambil Manhours per Bulan per Company (PT. MSM & PT. TTN) (Gambar Kedua)
     */
    private function loadMSMTTNData()
    {
        $companies = ['PT. MSM', 'PT. TTN'];
        $data = ['months' => array_map(fn($m) => Carbon::create()->month($m)->format('M'), range(1, 12))];

        foreach ($companies as $company) {
            $query = Manhour::query()
                ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours') // SUM(manhours) BUKAN COUNT(*)
                ->dateRange($this->start_date, $this->end_date)
                ->where('company', $company) // Filter per perusahaan
                // Tambahkan filter kategori jika diperlukan, tapi fokus ke nama perusahaan
                // ->where('company_category', 'CONTRACTOR')
                ->groupBy('month')
                ->orderBy('month');

            $results = $query->get();

            // Inisialisasi dan isi total bulanan
            $monthlyTotals = array_fill(1, 12, 0);
            foreach ($results as $item) {
                $monthlyTotals[$item->month] = (int)$item->total_manhours;
            }

            // Masukkan ke array data dengan key nama perusahaan
            $data[str_replace('.', '', strtolower($company))] = array_values($monthlyTotals);
        }

        $this->msmTTNData = json_encode($data);
        $this->dispatch('updateMSMTTNChart', $this->msmTTNData);
    }
    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
