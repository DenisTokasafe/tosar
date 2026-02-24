<?php

namespace App\Livewire\Administration\People;

use App\Models\Compliance as ModelsCompliance;
use App\Models\ComplianceMaster;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Compliance extends Component
{
    public $userId;
    public $compliance_class;
    public $compliance_name;
    public $start_date;
    public $isEditMode = false;
    public $complianceId;

    public function mount($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
    }

    /** * Lifecycle Hook:
     * Jika user mengubah dropdown Class, kita kosongkan pilihan Name
     */

    public function getExistingClassesProperty()
    {
        return ComplianceMaster::select('class')
            ->distinct()
            ->whereNotNull('class')
            ->orderBy('class', 'asc')
            ->pluck('class');
    }

    public function edit(ModelsCompliance $data)
    {
        $this->isEditMode = true;
        $this->complianceId = $data->id;

        // Load relasi master jika belum ter-load
        $data->load('master');

        // 1. Set Class (Ini akan memicu Computed Property getExistingNameProperty)
        $this->compliance_class = $data->master->class;

        // 2. Gunakan $this->fill() untuk memastikan sinkronisasi data ke View lebih stabil
        $this->compliance_class = $data->master->class;
        $this->compliance_name = $data->master->name;
        $this->start_date = Carbon::parse($data->start_date)->format('d-m-Y');

        $this->dispatch('open-modal-compliance');
    }

    public function openCreateModal()
    {
        $this->reset(['complianceId', 'compliance_name', 'compliance_class', 'start_date']);
        $this->isEditMode = false;
        $this->dispatch('open-modal-compliance');
    }
    public function save()
    {
        $this->validate([
            'compliance_name' => 'required',
            'start_date' => 'required',
        ]);

        $master = ComplianceMaster::where('name', $this->compliance_name)->first();
        // Gunakan createFromFormat jika input d-m-Y agar Carbon tidak bingung
        $startDate = Carbon::createFromFormat('d-m-Y', trim($this->start_date));

        $expiredAt = ($master->duration_months > 0)
            ? $startDate->copy()->addMonths($master->duration_months)
            : null;

        $data = [
            'user_id' => $this->userId,
            'compliance_master_id' => $master->id,
            'start_date' => $startDate->format('Y-m-d'),
            'expired_at' => $expiredAt ? $expiredAt->format('Y-m-d') : null,
            'status' => true,
        ];

        if ($this->isEditMode) {
            ModelsCompliance::find($this->complianceId)->update($data);
        } else {
            ModelsCompliance::create($data);
        }

        $this->dispatch('close-modal-compliance');
        $this->dispatch('alert', 'Data berhasil ' . ($this->isEditMode ? 'diperbarui' : 'disimpan'));
    }
    public function closed()
    {
        $this->reset(['complianceId', 'compliance_name', 'compliance_class', 'start_date']);
        $this->dispatch('close-modal-compliance');
    }

    public function render()
    {
        $master = [];
        $master = ComplianceMaster::select('name')->distinct()
            ->where('class', $this->compliance_class)
            ->whereNotNull('name')
            ->orderBy('name', 'asc')
            ->pluck('name');
        return view('livewire.administration.people.compliance', [
            'compliances' => ModelsCompliance::where('user_id', $this->userId)
                ->with('master')
                ->latest() // Menampilkan data terbaru di atas
                ->get(),
            'compliance_names' => $master
        ]);
    }
}
