<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use App\Models\Manhour;
use Livewire\Component;
use Livewire\Attributes\On;

class Index extends Component
{
    // Properti untuk menyimpan data yang dikirim ke ECharts
    public $manhoursChartData = '[]';
    public $manpowerChartData = '[]'; // Tambahkan ini jika Anda juga ingin memuat data Manpower
    public $start_date = null;
    public $end_date = null;

    public function mount()
    {
        // Default filter tanggal
        if (!$this->start_date || !$this->end_date) {
            $this->start_date = Carbon::now()->startOfYear()->toDateString();
            $this->end_date = Carbon::now()->endOfYear()->toDateString();
        }
        $this->loadManhoursData();
        // Anda mungkin ingin menambahkan $this->loadManpowerData(); di sini
    }

    #[On('dateRangeUpdated')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date = $data['end'];

        $this->loadManhoursData();
        // $this->loadManpowerData();
    }

    #[On('chartsDataRefresh')]
    public function loadManhoursData()
    {
        $dataMSM = $this->getMonthlyManhours('PT. MSM');
        $dataTTN = $this->getMonthlyManhours('PT. TTN');

        // Untuk total Contractor, kita bisa menjumlahkan data yang sudah ada atau query ulang
        // Query ulang untuk memastikan keakuratan jika ada perusahaan lain di 'CONTRACTOR'
        $dataContractor = $this->getMonthlyManhours(['PT. MSM', 'PT. TTN']);


        // Ambil list bulan dari 1 hingga 12 dan inisialisasi array data
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
        }

        // Siapkan data akhir untuk ECharts
        $data = [
            'months' => $months,
            'msm_manhours' => array_values($dataMSM),
            'ttn_manhours' => array_values($dataTTN),
            'contractor_manhours' => array_values($dataContractor),
        ];

        $this->manhoursChartData = json_encode($data);

        // Dispatch event untuk update grafik Manhours
        $this->dispatch('updateManhoursChart', $this->manhoursChartData);
    }

    /**
     * Helper function untuk mengambil total manhours bulanan
     * @param string|array $company
     * @return array
     */
    private function getMonthlyManhours($company)
    {
        // Inisialisasi array untuk 12 bulan
        $monthlyTotals = array_fill(1, 12, 0);

        $query = Manhour::query()
            ->selectRaw('MONTH(date) as month_num, SUM(manhours) as total_manhours')
            ->dateRange($this->start_date, $this->end_date)
            ->where('company_category', 'CONTRACTOR')
            ->groupBy('month_num')
            ->orderBy('month_num', 'asc');

        if (is_array($company)) {
            $query->whereIn('company', $company);
        } else {
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
