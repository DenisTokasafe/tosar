<?php

namespace App\Livewire\Dashboard;

use App\Models\Hazard as ModelsHazard;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Hazard extends Component
{
    public $total_hazard;
    public $range_date = '';
    public $start_date;
    public $end_date;
    public function updatedRangeDate($value)
    {
        if (!empty($value)) {
            // Gunakan separator yang konsisten dengan JS Anda
            $dates = explode(' Ke ', $value);

            if (count($dates) === 2) {
                try {
                    // Bersihkan spasi dan pastikan formatnya benar
                    $this->start_date = trim($dates[0]);
                    $this->end_date = trim($dates[1]);

                    $this->dispatch('dateRangeUpdated', [
                        'start' => $this->start_date,
                        'end'   => $this->end_date,
                    ]);
                } catch (\Exception $e) {
                    // Jika gagal parsing tanggal, reset
                    $this->resetDates();
                }
            }
        } else {
            $this->resetDates();
        }
    }

    // Pisahkan fungsi reset agar kode lebih rapi (DRY)
    private function resetDates()
    {
        $this->reset(['start_date', 'end_date']);
        $this->dispatch('dateRangeUpdated', [
            'start' => null,
            'end'   => null,
        ]);
    }
    public function clearFilter()
    {
        $this->resetDates();
    }
    public function render()
    {
        $totalHazard = ModelsHazard::when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        })->count();

        $hazardByStatus = ModelsHazard::when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        });
        $statusHazard = $hazardByStatus->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status')->toArray();
        return view('livewire.dashboard.hazard', [
            'totalHazard' => $totalHazard,
            'hazardByStatus' => $statusHazard,
            'latestHazardReports' => ModelsHazard::when($this->start_date && $this->end_date, function ($q) {
                $q->dateRange($this->start_date, $this->end_date);
            })->latest()->limit(5)->get(),
        ]);
    }
}
