<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Manhour;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

class Index extends Component
{
    public $data, $manpowerData;
    public $start_date;
    public $end_date;
    public $years;
    public $filterSearch = '';

    public function mount()
    {
        // Default filter tanggal
        if (!$this->start_date || !$this->end_date) {
            $this->start_date = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->end_date = Carbon::now()->endOfYear()->format('Y-m-d');
        }
        $this->years = Carbon::now()->year;
        $this->loadData();
        $this->loadDataManpower();
    }

    #[On('manhoursSearchUpdated')]
    public function updateSearch($search)
    {
        $this->filterSearch = $search;
        $this->loadDataManpower();
        $this->loadData();
    }

    #[On('dateRangeManhours')]
    public function updateDateRange($data)
    {
        if (!$data['start'] || !$data['end']) {
            $this->start_date = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->end_date = Carbon::now()->endOfYear()->format('Y-m-d');
        } else {
            $this->start_date = $data['start'];
            $this->end_date  = $data['end'];
        }
        $this->loadData();
        $this->loadDataManpower();
    }

    /**
     * Helper function to get the base query builder based on user role.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getBaseQuery(): Builder
    {
        Gate::authorize('viewAny', Manhour::class);
        $user = auth()->user();

        if ($user->roles()->where('role_id', 1)->exists()) {
            // Admin: Lihat semua data
            return Manhour::query();
        } else {
            // Kontraktor: Lihat hanya data kontraktornya sendiri
            $contractorNames = $user->contractors()->pluck('contractor_name');
            return Manhour::whereIn('company', $contractorNames);
        }
    }

    #[On('chartManhoursUpdate')]
    public function loadData()
    {
        // Gunakan getBaseQuery() untuk mendapatkan query builder yang sudah difilter peran
        $baseQuery = $this->getBaseQuery();

        // Query untuk mengambil bulan unik. Kita harus CLONE baseQuery
        $monthsRaw = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)
            ->selectRaw('DISTINCT MONTH(date) as month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();

        // Format bulan ke teks (Jan, Feb, Mar)
        $months = array_map(
            fn($m) => Carbon::create()->month($m)->format('M'),
            $monthsRaw
        );

        // --- Fungsi pembantu untuk mengambil data per bulan dengan filter ---
        $getMonthlyData = function (string $columnName, string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            $query = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch);

            if ($companyFilter) {
                $query->where('company', $companyFilter);
            }
            if ($categoryFilter) {
                $query->where('company_category', $categoryFilter);
            }

            // Perhatikan SELECT RAW: hanya mengambil kolom yang di GROUP BY (month) dan kolom agregat
            return $query->selectRaw("MONTH(date) as month, SUM({$columnName}) as total_data")
                ->groupBy('month')
                ->pluck('total_data', 'month')
                ->toArray();
        };

        // === PT. MSM (Manhours) ===
        $msmData = $getMonthlyData('manhours', 'PT. MSM');

        // === PT. TTN (Manhours) ===
        $ttnData = $getMonthlyData('manhours', 'PT. TTN');

        // === CONTRACTOR (Manhours) ===
        $contractorData = $getMonthlyData('manhours', null, 'CONTRACTOR');

        // Format data: pastikan semua bulan ada (0 jika kosong)
        $msm = [];
        $ttn = [];
        $contractor = [];
        foreach ($monthsRaw as $m) {
            $msm[]    = $msmData[$m] ?? 0;
            $ttn[]    = $ttnData[$m] ?? 0;
            $contractor[] = $contractorData[$m] ?? 0;
        }

        // --- Logika untuk Menonaktifkan Legend ---
        $hiddenLegends = [];
        if (array_sum($msm) === 0) {
            $hiddenLegends[] = 'PT. MSM';
        }
        if (array_sum($ttn) === 0) {
            $hiddenLegends[] = 'PT. TTN';
        }
        if (array_sum($contractor) === 0) {
            $hiddenLegends[] = 'CONTRACTOR';
        }

        // Data final untuk chart
        $payload = [
            'months' => $months,
            'msm'  => $msm,
            'ttn'  => $ttn,
            'contractor' => $contractor,
            'hidden_legends' => $hiddenLegends,
        ];

        $this->data = json_encode($payload);
        $this->dispatch('manhoursChart', $this->data);
    }

    #[On('chartManpowerUpdate')]
    public function loadDataManpower()
    {
        $baseQuery = $this->getBaseQuery(); // Ambil base query yang sudah difilter peran

        // Query untuk mengambil bulan unik. Kita harus CLONE baseQuery
        $monthsRaw = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->selectRaw('DISTINCT MONTH(date) as month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();

        // Format bulan ke teks (Jan, Feb, Mar)
        $months = array_map(
            fn($m) => Carbon::create()->month($m)->format('M'),
            $monthsRaw
        );

        // --- Fungsi pembantu untuk mengambil data per bulan dengan filter (untuk Manpower) ---
        $getMonthlyManpowerData = function (string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            $query = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch);

            if ($companyFilter) {
                $query->where('company', $companyFilter);
            }
            if ($categoryFilter) {
                $query->where('company_category', $categoryFilter);
            }

            // Perhatikan SELECT RAW: hanya mengambil kolom yang di GROUP BY (month) dan kolom agregat
            return $query->selectRaw("MONTH(date) as month, SUM(manpower) as total_manpower")
                ->groupBy('month')
                ->pluck('total_manpower', 'month')
                ->toArray();
        };

        // === PT. MSM (Manpower) ===
        $msmData = $getMonthlyManpowerData('PT. MSM');

        // === PT. TTN (Manpower) ===
        $ttnData = $getMonthlyManpowerData('PT. TTN');

        // === CONTRACTOR (Manpower) ===
        $contractorData = $getMonthlyManpowerData(null, 'CONTRACTOR');

        // Format data: pastikan semua bulan ada (0 jika kosong)
        $msm_mp = [];
        $ttn_mp = [];
        $contractor_mp = [];

        foreach ($monthsRaw as $m) {
            $msm_mp[] = $msmData[$m] ?? 0;
            $ttn_mp[] = $ttnData[$m] ?? 0;
            $contractor_mp[] = $contractorData[$m] ?? 0;
        }

        // --- Logika untuk Menonaktifkan Legend ---
        $hiddenLegends_mp = [];
        if (array_sum($msm_mp) === 0) {
            $hiddenLegends_mp[] = 'PT. MSM';
        }
        if (array_sum($ttn_mp) === 0) {
            $hiddenLegends_mp[] = 'PT. TTN';
        }
        if (array_sum($contractor_mp) === 0) {
            $hiddenLegends_mp[] = 'CONTRACTOR';
        }

        // Data final untuk chart
        $payload_manpower = [
            'months' => $months,
            'msm'  => $msm_mp,
            'ttn'  => $ttn_mp,
            'contractor' => $contractor_mp,
            'hidden_legends' => $hiddenLegends_mp,
        ];

        $this->manpowerData = json_encode($payload_manpower);
        $this->dispatch('manpowerChart', $this->manpowerData);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
