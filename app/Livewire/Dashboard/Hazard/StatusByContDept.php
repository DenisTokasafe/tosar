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
        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
    }
    #[On('hazardStatusByCont_Dept')]
    public function loadData()
    {
        // Logika untuk memuat data berdasarkan $this->start_date dan $this->end_date
        // Misalnya, ambil data dari model Hazard berdasarkan rentang tanggal
        $hazards = Hazard::with(['department', 'contractor'])->when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        })->whereYear('tanggal', Carbon::now()->year)->get();
        // Grouping data berdasarkan Departemen (atau gabungan Dept & Kontraktor)
        // 2. Kumpulkan semua label unik (Dept + Contractor)
        $deptNames = $hazards->whereNotNull('department_id')->pluck('department.department_name')->unique();
        $contNames = $hazards->whereNotNull('contractor_id')->pluck('contractor.contractor_name')->unique();

        // Gabungkan dan urutkan secara alfabetis
        $labels = $deptNames->merge($contNames)->sort()->values()->toArray();

        $closedData = [];
        $openData = [];

        // 3. Hitung status untuk setiap label
        foreach ($labels as $name) {
            // Cari laporan yang department-nya bernama $name ATAU contractor-nya bernama $name
            $reportsForLabel = $hazards->filter(function ($report) use ($name) {
                return ($report->department->name ?? '') === $name ||
                    ($report->contractor->name ?? '') === $name;
            });

            // Hitung Closed
            $closedData[] = $reportsForLabel->where('status', 'closed')->count();

            // Hitung Open (Bukan closed dan bukan cancel)
            $openData[] = $reportsForLabel->whereNotIn('status', ['closed', 'cancel'])->count();
        }

        $chartData = [
            'labels' => $labels,
            'closed' => $closedData,
            'open'   => $openData
        ];

        $this->status = json_encode($chartData);
        $this->dispatch('hazardStatus_DeptOrCont', $this->status);
    }
    public function render()
    {
        return view('livewire.dashboard.hazard.status-by-cont-dept');
    }
}
