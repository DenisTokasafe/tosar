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
        // 1. Ambil tanggal paling akhir dari database sebagai titik acuan
        // Kita gunakan model Manhour karena ini berada di komponen Manhours
        $lastDateRaw = Manhour::max('date');
        if ($lastDateRaw) {
            $lastDate = Carbon::parse($lastDateRaw);
            // 2. End date adalah tanggal terbaru yang ditemukan
            $this->end_date = $lastDate->format('Y-m-d');
            // 3. Start date ditarik mundur 11 bulan dari tanggal terbaru
            // Menggunakan startOfMonth agar mencakup data dari awal bulan tersebut
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');
        } else {
            // Fallback jika database masih kosong (menggunakan tanggal saat ini)
            $this->end_date = now()->format('Y-m-d');
            $this->start_date = now()->subMonths(11)->startOfMonth()->format('Y-m-d');
        }
        // Mengatur properti years agar tetap sinkron dengan tahun dari data terbaru
        $this->years = Carbon::parse($this->end_date)->year;
        // 4. Jalankan pengambilan data
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

    #[On('dateRangeManhours')] // Pastikan event name ini sesuai dengan yang dikirim dari JS/Blade
    public function updateDateRange($data)
    {
        // 1. Cek apakah start dan end dikirim dan tidak kosong
        if (!empty($data['start']) && !empty($data['end'])) {
            // Jika user memilih tanggal secara manual
            $this->start_date = $data['start'];
            $this->end_date   = $data['end'];

            // Reset filter tahun agar query fokus pada range tanggal spesifik
            $this->years = null;
        } else {
            // 2. Jika filter dihapus/kosong, kembalikan ke logika 12 bulan berjalan
            // Ambil tanggal terakhir dari database (Sesuaikan model: Manhour::max('date') atau Hazard::max('tanggal'))
            $lastDateRaw = Manhour::max('date');
            $lastDate = $lastDateRaw ? Carbon::parse($lastDateRaw) : Carbon::now();

            // Gunakan format Y-m-d agar sinkron dengan input date HTML5 atau database query
            $this->end_date   = $lastDate->format('Y-m-d');
            $this->start_date = $lastDate->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');

            // Pastikan years di-null agar loadData memprioritaskan whereBetween tanggal
            $this->years = null;
        }

        // 3. Refresh semua data grafik
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

        // Pastikan menggunakan model Manhour yang benar (sesuai namespace Anda)
        $query = Manhour::query();

        if ($user->roles()->where('role_id', 1)->exists()) {
            // Admin: Lihat semua data
            return $query;
        } else {
            // Kontraktor: Lihat hanya data kontraktor yang terhubung dengan user
            $contractorNames = $user->contractors()->pluck('contractor_name');
            return $query->whereIn('company', $contractorNames);
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
        $months = $monthsRaw->map(fn($m) => \Carbon\Carbon::create($m->year, $m->month, 1)->format('M y'))->toArray();

        // 2. Fungsi pembantu mengambil data (Gunakan CONCAT Year-Month sebagai Key untuk mapping)
        $getMonthlyData = function (string $columnName, string $companyFilter = null, string $categoryFilter = null) use ($baseQuery) {
            $query = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch);

            if ($companyFilter) $query->where('company', $companyFilter);
            if ($categoryFilter) $query->where('company_category', $categoryFilter);

            // Gunakan alias year_month untuk mempermudah pluck di PHP
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

        // --- Logika untuk Menonaktifkan Legend ---
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

        // 1. Ambil Tahun & Bulan unik secara kronologis
        $monthsRaw = (clone $baseQuery)->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $months = $monthsRaw->map(fn($m) => \Carbon\Carbon::create($m->year, $m->month, 1)->format('M y'))->toArray();

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
            $msm_mp[]        = $msmData[$key] ?? 0;
            $ttn_mp[]        = $ttnData[$key] ?? 0;
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
