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
    public $status;

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
                // Pastikan scope dateRange sudah benar di model Hazard
                $q->whereBetween('tanggal', [$this->start_date, $this->end_date]);
            })->get();

        // 1. Ambil label unik menggunakan kolom yang benar (department_name & contractor_name)
        $deptNames = $hazards->whereNotNull('department_id')->pluck('department.department_name')->unique();
        $contNames = $hazards->whereNotNull('contractor_id')->pluck('contractor.contractor_name')->unique();

        // Gabungkan, hilangkan nilai kosong, dan urutkan
        $labels = $deptNames->merge($contNames)->filter()->sort()->values()->toArray();

        $closedData = [];
        $openData = [];

        // 2. Hitung status untuk setiap label
        foreach ($labels as $name) {
            $reportsForLabel = $hazards->filter(function ($report) use ($name) {
                // Cek kecocokan pada kedua kolom relasi
                $isDept = ($report->department->department_name ?? '') === $name;
                $isCont = ($report->contractor->contractor_name ?? '') === $name;
                return $isDept || $isCont;
            });

            // Hitung Closed
            $closedData[] = $reportsForLabel->where('status', 'closed')->count();

            // Hitung Open (Semua kecuali closed dan cancel)
            $openData[] = $reportsForLabel->whereNotIn('status', ['closed', 'cancel'])->count();
        }

        $chartData = [
            'labels' => $labels,
            'closed' => $closedData,
            'open'   => $openData
        ];

        $this->status = json_encode($chartData);
        // Dispatch event ke browser/AlpineJS
        $this->dispatch('hazardStatus_DeptOrCont',$this->status);
    }

    public function render()
    {
        return view('livewire.dashboard.hazard.status-by-cont-dept');
    }
}
