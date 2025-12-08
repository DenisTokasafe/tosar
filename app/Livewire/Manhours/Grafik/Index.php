<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Manhour;

class ManhoursChart extends Component
{
    // Properti untuk menyimpan data yang dikirim ke ECharts
    public $combinedChartData = '[]';
    public $start_date = null;
    public $end_date = null;

    public function mount()
    {
        // Default filter tanggal
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
        // --- 1. Ambil Data Manhours Perusahaan (PT. MSM & PT. TTN) ---
        $dataMSM = $this->getMonthlyManhours('PT. MSM');
        $dataTTN = $this->getMonthlyManhours('PT. TTN');

        // --- 2. Ambil Data Total SELURUH Contractor ---
        $dataAllContractor = $this->getMonthlyManhours(null, true); // Parameter kedua menandakan ambil SEMUA contractor

        // --- 3. Format Data Akhir ---

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
        }

        $data = [
            'months' => $months,
            'msm_manhours' => array_values($dataMSM),
            'ttn_manhours' => array_values($dataTTN),
            'all_contractor_manhours' => array_values($dataAllContractor),
        ];

        $this->combinedChartData = json_encode($data);

        // Dispatch event untuk update grafik
        $this->dispatch('updateCombinedChart', $this->combinedChartData);
    }

    /**
     * Helper function untuk mengambil total manhours bulanan
     * @param string|null $company Nama perusahaan (misal: 'PT. MSM')
     * @param bool $allContractors Jika true, ambil semua company_category = CONTRACTOR
     * @return array
     */
    private function getMonthlyManhours(?string $company = null, bool $allContractors = false): array
    {
        // Inisialisasi array untuk 12 bulan
        $monthlyTotals = array_fill(1, 12, 0);

        $query = Manhour::query()
            ->selectRaw('MONTH(date) as month_num, SUM(manhours) as total_manhours')
            ->dateRange($this->start_date, $this->end_date)
            ->groupBy('month_num')
            ->orderBy('month_num', 'asc');

        if ($allContractors) {
            // Filter untuk semua perusahaan dengan kategori CONTRACTOR
            $query->where('company_category', 'CONTRACTOR');
        } elseif ($company) {
            // Filter untuk perusahaan spesifik
            $query->where('company', $company);
        }

        $results = $query->get();

        foreach ($results as $item) {
            $monthlyTotals[$item->month_num] = (int)$item->total_manhours;
        }

        return $monthlyTotals;
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
