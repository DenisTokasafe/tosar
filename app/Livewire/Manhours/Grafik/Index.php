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
        // 1. Logika Rolling 12 Bulan dari data terakhir di database
        $lastDateRaw = Manhour::max('date');

        if ($lastDateRaw) {
            $lastDate = Carbon::parse($lastDateRaw);
            $this->end_date = $lastDate->format('Y-m-d');
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');
        } else {
            $this->start_date = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->end_date = Carbon::now()->endOfYear()->format('Y-m-d');
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
            $this->mount(); // Reset ke default rolling 12 bulan
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

        // Ambil Tahun & Bulan unik agar urutan kronologis benar
        $monthsRaw = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthsLabels = $monthsRaw->map(fn($m) => Carbon::create($m->year, $m->month, 1)->format('M y'))->toArray();

        // --- Fungsi pembantu untuk mengambil data per bulan dengan filter ---
        $getMonthlyData = function (string $columnName, string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            // Clone base query agar tidak merusak query asli
            $query = (clone $baseQuery)
                ->dateRange($this->start_date, $this->end_date)
                ->search($this->filterSearch);

            // Gunakan fungsi where standar agar Laravel otomatis memberi tanda kutip (Binding)
            if ($companyFilter) {
                $query->where('company', $companyFilter);
            }

            if ($categoryFilter) {
                $query->where('company_category', $categoryFilter);
            }

            // Gunakan selectRaw hanya untuk bagian kalkulasi dan alias
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
            $msm[] = $msmData[$key] ?? 0;
            $ttn[] = $ttnData[$key] ?? 0;
            $contractor[] = $contractorData[$key] ?? 0;
        }

        $hiddenLegends = [];
        if (array_sum($msm) === 0) $hiddenLegends[] = 'PT. MSM';
        if (array_sum($ttn) === 0) $hiddenLegends[] = 'PT. TTN';
        if (array_sum($contractor) === 0) $hiddenLegends[] = 'CONTRACTOR';

        $this->data = json_encode([
            'months' => $monthsLabels,
            'msm' => $msm,
            'ttn' => $ttn,
            'contractor' => $contractor,
            'hidden_legends' => $hiddenLegends,
        ]);
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

        $monthsLabels = $monthsRaw->map(fn($m) => Carbon::create($m->year, $m->month, 1)->format('M y'))->toArray();

        $getMonthlyManpowerData = function (string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            $query = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch);

            if ($companyFilter) $query->where('company', $companyFilter);
            if ($categoryFilter) $query->where('company_category', $categoryFilter);

            return $query->selectRaw("CONCAT(YEAR(date), '-', MONTH(date)) as year_month, SUM(manpower) as total_manpower")
                ->groupByRaw('YEAR(date), MONTH(date)')
                ->pluck('total_manpower', 'year_month')
                ->toArray();
        };

        $msm_mp_raw = $getMonthlyManpowerData('PT. MSM');
        $ttn_mp_raw = $getMonthlyManpowerData('PT. TTN');
        $contractor_mp_raw = $getMonthlyManpowerData(null, 'CONTRACTOR');

        $msm_mp = [];
        $ttn_mp = [];
        $contractor_mp = [];
        foreach ($monthsRaw as $m) {
            $key = $m->year . '-' . $m->month;
            $msm_mp[] = $msm_mp_raw[$key] ?? 0;
            $ttn_mp[] = $ttn_mp_raw[$key] ?? 0;
            $contractor_mp[] = $contractor_mp_raw[$key] ?? 0;
        }

        $hiddenLegends_mp = [];
        if (array_sum($msm_mp) === 0) $hiddenLegends_mp[] = 'PT. MSM';
        if (array_sum($ttn_mp) === 0) $hiddenLegends_mp[] = 'PT. TTN';
        if (array_sum($contractor_mp) === 0) $hiddenLegends_mp[] = 'CONTRACTOR';

        $this->manpowerData = json_encode([
            'months' => $monthsLabels,
            'msm' => $msm_mp,
            'ttn' => $ttn_mp,
            'contractor' => $contractor_mp,
            'hidden_legends' => $hiddenLegends_mp,
        ]);
        $this->dispatch('manpowerChart', $this->manpowerData);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
