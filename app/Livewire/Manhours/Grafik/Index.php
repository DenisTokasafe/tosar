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
        // Ambil tanggal paling akhir dari database sebagai titik acuan
        $lastDateRaw = Manhour::max('tanggal');

        if ($lastDateRaw) {
            $lastDate = Carbon::parse($lastDateRaw);

            // Set end_date ke tanggal terbaru
            $this->end_date = $lastDate->format('Y-m-d');

            // Set start_date ke 11 bulan sebelumnya (total 12 bulan berjalan)
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');
        } else {
            // Fallback jika database kosong (menggunakan waktu sekarang)
            $this->start_date = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
            $this->end_date = Carbon::now()->format('Y-m-d');
        }

        $this->years = Carbon::parse($this->end_date)->year;

        $this->loadData();
        $this->loadDataManpower();
    }
    #[On('dateRangeManhours')]
    public function updateDateRange($data)
    {
        if (empty($data['start']) || empty($data['end'])) {
            // Jika filter dihapus (kosong), kembalikan ke logika 12 bulan berjalan
            $lastDateRaw = Manhour::max('tanggal');
            $lastDate = $lastDateRaw ? Carbon::parse($lastDateRaw) : Carbon::now();

            $this->end_date = $lastDate->format('Y-m-d');
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');

            // Set years mengikuti tahun dari data terakhir
            $this->years = $lastDate->year;
        } else {
            // Jika user memilih tanggal secara manual
            $this->start_date = $data['start'];
            $this->end_date   = $data['end'];

            // Reset years agar filter tanggal lebih diprioritaskan
            $this->years = null;
        }

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
        $baseQuery = $this->getBaseQuery();

        // 1. Ambil kombinasi Tahun & Bulan unik agar urutan kronologis benar
        $monthsRaw = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format label bulan (contoh: Jan 25)
        $months = $monthsRaw->map(fn($m) => Carbon::create($m->year, $m->month, 1)->format('M y'))->toArray();

        // --- Fungsi pembantu diperbarui untuk group by Year dan Month ---
        $getMonthlyData = function (string $columnName, string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            $query = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch);

            if ($companyFilter) $query->where('company', $companyFilter);
            if ($categoryFilter) $query->where('company_category', $categoryFilter);

            // Key pluck menggunakan kombinasi Year-Month
            return $query->selectRaw("CONCAT(YEAR(date), '-', MONTH(date)) as year_month, SUM({$columnName}) as total_data")
                ->groupBy('year_month')
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

        // ... (Logika Hidden Legends tetap sama)

        $payload = [
            'months' => $months,
            'msm'    => $msm,
            'ttn'    => $ttn,
            'contractor' => $contractor,
            'hidden_legends' => $hiddenLegends ?? [],
        ];

        $this->data = json_encode($payload);
        $this->dispatch('manhoursChart', $this->data);
    }

    #[On('chartManpowerUpdate')]
    public function loadDataManpower()
    {
        $baseQuery = $this->getBaseQuery();

        // 1. Ambil Tahun & Bulan unik secara kronologis
        $monthsRaw = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $months = $monthsRaw->map(fn($m) => Carbon::create($m->year, $m->month, 1)->format('M y'))->toArray();

        $getMonthlyManpowerData = function (string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            $query = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch);

            if ($companyFilter) $query->where('company', $companyFilter);
            if ($categoryFilter) $query->where('company_category', $categoryFilter);

            return $query->selectRaw("CONCAT(YEAR(date), '-', MONTH(date)) as year_month, SUM(manpower) as total_manpower")
                ->groupBy('year_month')
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
            $msm_mp[]        = $msmData[$key] ?? 0;
            $ttn_mp[]        = $ttnData[$key] ?? 0;
            $contractor_mp[] = $contractor_mpData[$key] ?? 0; // Perbaikan variabel contractor
        }

        // ... (Logika Hidden Legends tetap sama)

        $payload_manpower = [
            'months' => $months,
            'msm'    => $msm_mp,
            'ttn'    => $ttn_mp,
            'contractor' => $contractor_mp,
            'hidden_legends' => $hiddenLegends_mp ?? [],
        ];

        $this->manpowerData = json_encode($payload_manpower);
        $this->dispatch('manpowerChart', $this->manpowerData);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
