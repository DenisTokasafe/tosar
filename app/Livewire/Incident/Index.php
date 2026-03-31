<?php

namespace App\Livewire\Incident;

use App\Models\Department;
use App\Models\EventSubType;
use App\Models\EventType;
use App\Models\IncidentReport;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $search;
    public $filterEventType = [];
    public $filterEventSubType = [];
    public $filterDept = [];
    public $filterStatus = [];

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
        // 1. Ambil data untuk opsi filter di header
        $filterOptions = [
            'departments'   => Department::select('id', 'department_name')->get(),
            'eventTypes'    => EventType::select('id', 'event_type_name')->get(),
            'eventSubTypes' => EventSubType::select('id', 'event_sub_type_name')->get(),
        ];

        // 2. Query utama dengan filter
        $incidents = IncidentReport::query()
            // Eager Load relasi agar tidak lambat (N+1)
            ->with(['eventType', 'eventSubType', 'department', 'pic'])

            // Filter Pencarian (Nomor Referensi)
            ->when($this->search, function ($query) {
                $query->where('report_number', 'like', '%' . $this->search . '%');
            })

            // Filter Status (Multiple Selection / Array)
            ->when($this->filterStatus, function ($query) {
                // Menggunakan whereIn karena user bisa pilih lebih dari satu status
                $query->whereIn('status', (array) $this->filterStatus);
            })

            // Filter Departemen / Divisi
            ->when($this->filterDept, function ($query) {
                $query->whereIn('department_id', (array) $this->filterDept);
            })

            // Filter Tipe Insiden
            ->when($this->filterEventType, function ($query) {
                $query->whereIn('event_type_id', (array) $this->filterEventType);
            })

            // Filter Sub Tipe / Klasifikasi
            ->when($this->filterEventSubType, function ($query) {
                $query->whereIn('event_sub_type_id', (array) $this->filterEventSubType);
            })

            ->latest('date_time')
            ->paginate(20);

        return view('livewire.incident.index', [
            'incidents'     => $incidents,
            'filterOptions' => $filterOptions // Kirim data filter ke view
        ]);
    }
    public function paginationView()
    {
        return 'paginate.pagination';
    }
}
