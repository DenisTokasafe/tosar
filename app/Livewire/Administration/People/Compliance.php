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

        // ❗ PINDAHKAN INI KE ATAS: Set $this->userId DULU
        $this->userId = $user->id;
    }
    public function getExistingClassesProperty()
    {
        return ComplianceMaster::select('class')
            ->distinct()
            ->whereNotNull('class')
            ->orderBy('class', 'asc')
            ->pluck('class');
    }
    public function getExistingNameProperty()
    {
        return ComplianceMaster::select('name')
            ->distinct()
            ->whereNotNull('name')
            ->where('class', $this->compliance_class)
            ->orderBy('name', 'asc')
            ->pluck('name');
    }
    public function openCreateModal()
{
    $this->reset(['complianceId', 'compliance_name', 'compliance_class', 'start_date']);
    $this->isEditMode = false;
    $this->dispatch('open-modal-compliance'); // Event untuk membuka modal
}

// Fungsi untuk membuka modal Edit
public function edit($id)
{
    $this->isEditMode = true;
    $this->complianceId = $id;

    $data = Compliance::with('master')->findOrFail($id);

    // Isi field form dengan data yang ada
    $this->compliance_class = $data->master->class;
    $this->compliance_name = $data->master->name;
    $this->start_date = Carbon::parse($data->start_date)->format('d-m-Y');

    $this->dispatch('open-modal-compliance');
}

public function save()
{
    $this->validate([
        'compliance_name' => 'required',
        'start_date' => 'required',
    ]);

    $master = ComplianceMaster::where('name', $this->compliance_name)->first();
    $startDate = Carbon::parse($this->start_date);
    $expiredAt = ($master->duration_months > 0) ? $startDate->copy()->addMonths($master->duration_months) : null;

    $data = [
        'user_id' => $this->userId,
        'compliance_master_id' => $master->id,
        'start_date' => $startDate->format('Y-m-d'),
        'expired_at' => $expiredAt ? $expiredAt->format('Y-m-d') : null,
        'status' => true,
    ];

    if ($this->isEditMode) {
        // Logika UPDATE
        ModelsCompliance::find($this->complianceId)->update($data);
    } else {
        // Logika CREATE
        ModelsCompliance::create($data);
    }

    $this->dispatch('close-modal-compliance');
    $this->dispatch('notify', 'Data berhasil ' . ($this->isEditMode ? 'diperbarui' : 'disimpan'));
}
    public function render()
    {
        return view('livewire.administration.people.compliance', [
            'compliances' => ModelsCompliance::where('user_id', $this->userId)->with('master')->get()
        ]);
    }
}
