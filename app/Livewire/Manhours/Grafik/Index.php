<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Manhour;

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
            $this->end_date   = $data['end'];
        }
        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
        $this->loadDataManpower();
    }
    #[On('chartManhoursUpdate')]
    public function loadData()
    {
        // authorize viewAny
        Gate::authorize('viewAny', Manhour::class);
        $user = auth()->user();

        if ($user->roles()->where('role_id', 1)->exists()) {
            $query = Manhour::query();
        } else {
            $contractorNames = $user->contractors()->pluck('contractor_name');
            $query = Manhour::whereIn('company', $contractorNames);
        }
        /// Ambil bulan unique berdasarkan tanggal dan rentang filter
        $monthsRaw = $query->dateRange($this->start_date, $this->end_date)
            ->selectRaw('DISTINCT MONTH(date) as month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();
        // Format bulan ke teks (Jan, Feb, Mar)
        $months = array_map(
            fn($m) =>
            Carbon::create()->month($m)->format('M'),
            $monthsRaw
        );
        // === PT. MSM ===
        $msmData = $query->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company', 'PT. MSM')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();
        // === PT. TTN ===
        $ttnData = $query->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company', 'PT. TTN')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();
        // === CONTRACTOR ===
        $contractorData = $query->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company_category', 'CONTRACTOR')
            ->selectRaw('MONTH(date) as month, SUM(manhours) as total_manhours')
            ->groupBy('month')
            ->pluck('total_manhours', 'month')
            ->toArray();
        // Format data: pastikan semua bulan ada (0 jika kosong)
        $msm = [];
        $ttn = [];
        $contractor = [];
        foreach ($monthsRaw as $m) {
            $msm[]        = $msmData[$m] ?? 0;
            $ttn[]        = $ttnData[$m] ?? 0;
            $contractor[] = $contractorData[$m] ?? 0;
        }
        // --- Pemeriksaan Data Total untuk Legend ---
        $totalMsm = array_sum($msm);
        $totalTtn = array_sum($ttn);
        $totalContractor = array_sum($contractor);
        // Data final untuk chart
        $payload = [
            'months' => $months,
            'msm'    => $msm,
            'ttn'    => $ttn,
            'contractor' => $contractor,
            // Tambahkan informasi legend yang harus disembunyikan
            'hidden_legends' => [],
        ];

        if ($totalMsm === 0) {
            $payload['hidden_legends'][] = 'PT. MSM';
        }
        if ($totalTtn === 0) {
            $payload['hidden_legends'][] = 'PT. TTN';
        }
        if ($totalContractor === 0) {
            $payload['hidden_legends'][] = 'CONTRACTOR';
        }

        $this->data = json_encode($payload);
        $this->dispatch('manhoursChart', $this->data);
    }
    #[On('chartManpowerUpdate')] // Ganti nama event agar tidak bentrok
    public function loadDataManpower()
    {
Gate::authorize('viewAny', Manhour::class);
        $user = auth()->user();

        if ($user->roles()->where('role_id', 1)->exists()) {
            $query = Manhour::query();
        } else {
            $contractorNames = $user->contractors()->pluck('contractor_name');
            $query = Manhour::whereIn('company', $contractorNames);
        }
        /// Ambil bulan unique berdasarkan tanggal dan rentang filter
        $monthsRaw = $query->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->selectRaw('DISTINCT MONTH(date) as month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();

        // Format bulan ke teks (Jan, Feb, Mar)
        $months = array_map(
            fn($m) => Carbon::create()->month($m)->format('M'),
            $monthsRaw
        );

        // === PT. MSM ===
        $msmData = $query->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company', 'PT. MSM')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();

        // === PT. TTN ===
        $ttnData = $query->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company', 'PT. TTN')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();

        // === CONTRACTOR ===
        $contractorData = $query->dateRange($this->start_date, $this->end_date)->search($this->filterSearch)
            ->where('company_category', 'CONTRACTOR')
            ->selectRaw('MONTH(date) as month, SUM(manpower) as total_manpower')
            ->groupBy('month')
            ->pluck('total_manpower', 'month')
            ->toArray();

        // Format data: pastikan semua bulan ada (0 jika kosong)
        $msm_mp = []; // Menggunakan suffix _mp (manpower) untuk menghindari konflik nama
        $ttn_mp = [];
        $contractor_mp = [];

        foreach ($monthsRaw as $m) {
            $msm_mp[] = $msmData[$m] ?? 0;
            $ttn_mp[] = $ttnData[$m] ?? 0;
            $contractor_mp[] = $contractorData[$m] ?? 0;
        }
       // --- Pemeriksaan Data Total untuk Legend ---
    $totalMsm_mp = array_sum($msm_mp);
    $totalTtn_mp = array_sum($ttn_mp);
    $totalContractor_mp = array_sum($contractor_mp);

    // Data final untuk chart (Gunakan properti yang berbeda jika chart manpower dipisah)
    $payload_manpower = [
        'months' => $months,
        'msm'    => $msm_mp,
        'ttn'    => $ttn_mp,
        'contractor' => $contractor_mp,
        // Tambahkan informasi legend yang harus disembunyikan
        'hidden_legends' => [],
    ];

    if ($totalMsm_mp === 0) {
        $payload_manpower['hidden_legends'][] = 'PT. MSM';
    }
    if ($totalTtn_mp === 0) {
        $payload_manpower['hidden_legends'][] = 'PT. TTN';
    }
    if ($totalContractor_mp === 0) {
        $payload_manpower['hidden_legends'][] = 'CONTRACTOR';
    }


    // Gunakan properti Livewire yang berbeda, misalnya $manpowerChartData
    $this->manpowerData = json_encode($payload_manpower);
    // Dispatch event yang berbeda untuk grafik manpower
    $this->dispatch('manpowerChart', $this->manpowerData);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
