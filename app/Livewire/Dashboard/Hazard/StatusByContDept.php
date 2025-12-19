<?php

namespace App\Livewire\Dashboard\Hazard;

use Carbon\Carbon;
use App\Models\Hazard;
use Livewire\Component;
use Livewire\Attributes\On;

class StatusByContDept extends Component
{
    public $start_date;
    public $end_date;
    public $statusDeptCont;

    #[On('dateRangeUpdated')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date   = $data['end'];
        $this->loadData();
    }

    // Trigger awal saat komponen dimuat
    public function mount()
    {
        $this->loadData();
    }

    #[On('hazardStatusByCont_Dept')]
    public function loadData()
    {
        $hazards = Hazard::with(['department', 'contractor'])
            ->whereYear('tanggal', Carbon::now()->year)
            ->when($this->start_date && $this->end_date, function ($q) {
                $q->whereBetween('tanggal', [$this->start_date, $this->end_date]);
            })->get();

        // 1. Ambil label unik (Dept + Contractor)
        $deptNames = $hazards->whereNotNull('department_id')->pluck('department.department_name')->unique();
        $contNames = $hazards->whereNotNull('contractor_id')->pluck('contractor.contractor_name')->unique();
        $labels = $deptNames->merge($contNames)->filter()->unique()->toArray();

        $tempData = [];

        // 2. Hitung status dan simpan sementara untuk sorting
        foreach ($labels as $name) {
            $reportsForLabel = $hazards->filter(function ($report) use ($name) {
                $isDept = ($report->department->department_name ?? '') === $name;
                $isCont = ($report->contractor->contractor_name ?? '') === $name;
                return $isDept || $isCont;
            });

            $closed = $reportsForLabel->where('status', 'closed')->count();
            $open = $reportsForLabel->whereNotIn('status', ['closed', 'cancel'])->count();

            $tempData[] = [
                'label' => $name,
                'closed' => $closed,
                'open' => $open,
                'total' => $closed + $open
            ];
        }

        // 3. URUTKAN: Dari total terbesar ke terkecil
        usort($tempData, fn($a, $b) => $b['total'] <=> $a['total']);

        // 4. Pecah kembali menjadi format chartData
        $chartData = [
            'labels' => array_column($tempData, 'label'),
            'closed' => array_column($tempData, 'closed'),
            'open'   => array_column($tempData, 'open'),
        ];

        $this->statusDeptCont = json_encode($chartData);
        $this->dispatch('hazardStatus_DeptOrCont', $this->statusDeptCont);
    }

    public function render()
    {
        return view('livewire.dashboard.hazard.status-by-cont-dept');
    }
}
