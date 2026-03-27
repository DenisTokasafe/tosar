<?php

namespace App\Livewire\Incident;

use App\Models\IncidentReport;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $search = '';
    public $filterCategory = '';
    public $filterStatus = '';

    // Reset halaman jika filter berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function editIncident($id)
    {
        // Contoh: Redirect ke halaman form edit
        return redirect()->route('incident-detail', $id);
    }
    public function render()
    {
        $incidents = IncidentReport::query()
            ->with(['category', 'causer']) // Eager loading relasi
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('report_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest('date_time')
            ->paginate(20);
        return view('livewire.incident.index', [
            'incidents' => $incidents
        ]);
    }
}
