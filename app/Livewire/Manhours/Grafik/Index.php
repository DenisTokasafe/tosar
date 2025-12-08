<?php

namespace App\Livewire\Manhours\Grafik;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Manhour;

class Index extends Component
{
    public $data;
    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->loadData();
    }
    #[On('dateRangeManhours')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date   = $data['end'];
        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
    }
    #[On('chartManhoursUpdate')]
    public function loadData()
    {
        $dataManhours = Manhour::query()
        ->whereRaw(
            "STR_TO_DATE(`date`, '%Y/%m/%d') BETWEEN
             STR_TO_DATE(?, '%Y/%m/%d') AND STR_TO_DATE(?, '%Y/%m/%d')",
            [$this->start_date, $this->end_date]
        )
        ->selectRaw('
            MONTH(STR_TO_DATE(`date`, "%Y/%m/%d")) as month,
            company,
            COUNT(*) as total
        ')
        ->groupBy('month', 'company')
        ->orderBy('month')
        ->get();

    // ---- Ambil data per company ----
    $msmData = $dataManhours->where('company', 'PT. MSM')->pluck('total','month')->toArray();
    $ttnData = $dataManhours->where('company', 'PT. TTN')->pluck('total','month')->toArray();
    $contractorData = $dataManhours->where('company_category', 'Contractor')->pluck('total','month')->toArray();

    // ---- Ambil bulan dari database ----
    $monthsRaw = $dataManhours->pluck('month')->unique()->sort()->values()->toArray();

    // ---- Konversi ke nama bulan (Jan, Feb, ...) ----
    $months = collect($monthsRaw)
        ->map(fn($m) => Carbon::create()->month($m)->format('M'))
        ->toArray();

    // ---- Isi data berdasarkan bulan dari database ----
    $msm = [];
    $ttn = [];
    $contractor = [];

    foreach ($monthsRaw as $m) {
        $msm[]        = $msmData[$m] ?? 0;
        $ttn[]        = $ttnData[$m] ?? 0;
        $contractor[] = $contractorData[$m] ?? 0;
    }

    // ---- Kirim ke Chart ----
    $this->data = json_encode([
        'months'     => $months,
        'msm'        => $msm,
        'ttn'        => $ttn,
        'contractor' => $contractor,
    ]);

    $this->dispatch('manhoursChart', $this->data);
    }

    public function render()
    {
        return view('livewire.manhours.grafik.index');
    }
}
