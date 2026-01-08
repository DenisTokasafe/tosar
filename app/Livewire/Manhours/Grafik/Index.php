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
        // 1. Ambil tanggal paling akhir dari database sebagai titik acuan dashboard
        $lastDateRaw = Manhour::max('date');

        if ($lastDateRaw) {
            $lastDate = Carbon::parse($lastDateRaw);
            $this->end_date = $lastDate->format('Y-m-d');
            // Rolling 12 bulan (11 bulan kebelakang + bulan berjalan)
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');
        } else {
            // Fallback jika database kosong
            $this->start_date = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
            $this->end_date = Carbon::now()->format('Y-m-d');
        }

        $this->years = Carbon::parse($this->end_date)->year;
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
        if (empty($data['start']) || empty($data['end'])) {
            // Jika filter dikosongkan, kembalikan ke logika 12 bulan berjalan
            $lastDateRaw = Manhour::max('date');
            $lastDate = $lastDateRaw ? Carbon::parse($lastDateRaw) : Carbon::now();

            $this->end_date = $lastDate->format('Y-m-d');
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');
        } else {
            $this->start_date = $data['start'];
            $this->end_date  = $data['end'];
        }

        $this->loadData();
        $this->loadDataManpower();
    }

    private function getBaseQuery(): Builder
    {
        Gate::authorize('viewAny', Manhour::class);
        $user = auth()->user();

        if ($user->roles()->where('role_id', 1)->exists()) {
            return Manhour::query();
        } else {
            $contractorNames = $user->contractors()->pluck('contractor_name');
            return Manhour::whereIn('company', $contractorNames);
        }
    }

    #[On('chartManhoursUpdate')]
    public function loadData()
    {
        $baseQuery = $this->getBaseQuery();

        // 1. Ambil Tahun & Bulan unik agar urutan kronologis benar (Jan 25, Feb 25, dst)
        $monthsRaw = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format label sumbu X (contoh: Jan 25)
        $months = $monthsRaw->map(fn($m) => Carbon::create($m->year, $m->month, 1)->format('M y'))->toArray();

        // 2. Fungsi pembantu mengambil data (Gunakan CONCAT Year-Month sebagai Key)
        $getMonthlyData = function (string $columnName, string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            $query = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch);

            if ($companyFilter) {
                // Laravel akan otomatis membungkus 'PT. MSM' dengan tanda kutip
                $query->where('company', $companyFilter);
            }

            if ($categoryFilter) {
                $query->where('company_category', $categoryFilter);
            }

            return $query->selectRaw("CONCAT(YEAR(date), '-', MONTH(date)) as year_month, SUM({$columnName}) as total_data")
                ->groupByRaw('YEAR(date), MONTH(date)')
                ->pluck('total_data', 'year_month')
                ->toArray();
        };

        $msmData = $getMonthlyData('manhours', 'PT. MSM');
        $ttnData = $getMonthlyData('manhours', 'PT. TTN');
        $contractorData = $getMonthlyData('manhours', null, 'CONTRACTOR');

        $msm = [];
        $ttn = [];
        $contractor = [];

        foreach ($monthsRaw as $m) {
            $key = $m->year . '-' . $m->month;
            $msm[]        = $msmData[$key] ?? 0;
            $ttn[]        = $ttnData[$key] ?? 0;
            $contractor[] = $contractorData[$key] ?? 0;
        }

        // --- Logika Legend ---
        $hiddenLegends = [];
        if (array_sum($msm) === 0) $hiddenLegends[] = 'PT. MSM';
        if (array_sum($ttn) === 0) $hiddenLegends[] = 'PT. TTN';
        if (array_sum($contractor) === 0) $hiddenLegends[] = 'CONTRACTOR';

        $payload = [
            'months' => $months,
            'msm'    => $msm,
            'ttn'    => $ttn,
            'contractor' => $contractor,
            'hidden_legends' => $hiddenLegends,
        ];

        $this->data = json_encode($payload);
        $this->dispatch('manhoursChart', $this->data);
    }

    #[On('chartManpowerUpdate')]
    public function loadDataManpower()
    {
        $baseQuery = $this->getBaseQuery();

        $monthsRaw = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $months = $monthsRaw->map(fn($m) => Carbon::create($m->year, $m->month, 1)->format('M y'))->toArray();

        $getMonthlyManpowerData = function (string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            $query = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch);

            if ($companyFilter) $query->where('company', $companyFilter);
            if ($categoryFilter) $query->where('company_category', $categoryFilter);

            return $query->selectRaw("CONCAT(YEAR(date), '-', MONTH(date)) as year_month, SUM(manpower) as total_manpower")
                ->groupByRaw('YEAR(date), MONTH(date)')
                ->pluck('total_manpower', 'year_month')
                ->toArray();
        };

        $msmData = $getMonthlyManpowerData('PT. MSM');
        $ttnData = $getMonthlyManpowerData('PT. TTN');
        $contractorData = $getMonthlyManpowerData(null, 'CONTRACTOR');

        $msm_mp = [];
        $ttn_mp = [];
        $contractor_mp = [];

        foreach ($monthsRaw as $m) {
            $key = $m->year . '-' . $m->month;
            $msm_mp[] = $msmData[$key] ?? 0;
            $ttn_mp[] = $ttnData[$key] ?? 0;
            $contractor_mp[] = $contractorData[$key] ?? 0;
        }

        $hiddenLegends_mp = [];
        if (array_sum($msm_mp) === 0) $hiddenLegends_mp[] = 'PT. MSM';
        if (array_sum($ttn_mp) === 0) $hiddenLegends_mp[] = 'PT. TTN';
        if (array_sum($contractor_mp) === 0) $hiddenLegends_mp[] = 'CONTRACTOR';

        $payload_manpower = [
            'months' => $months,
            'msm'    => $msm_mp,
            'ttn'    => $ttn_mp,
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
