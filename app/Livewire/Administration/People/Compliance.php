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
    public function updatedComplianceClass()
    {
        $this->compliance_name = '';
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
        // Jika class belum dipilih, jangan tampilkan apa-apa atau tampilkan semua
        if (!$this->compliance_class) return collect();

        return ComplianceMaster::select('name')
            ->distinct()
            ->whereNotNull('name')
            // Menggunakan '=' lebih presisi daripada 'like' untuk dropdown filter
            ->where('class', $this->compliance_class)
            ->orderBy('name', 'asc')
            ->pluck('name');
    }

    public function openCreateModal()
    {
        $this->reset(['complianceId', 'compliance_name', 'compliance_class', 'start_date']);
        $this->isEditMode = false;
        $this->dispatch('open-modal-compliance');
    }

    public function edit($id)
    {
        $this->isEditMode = true;
        $this->complianceId = $id;

        // Pastikan nama relasi di Model Compliance adalah 'master'
        $data = ModelsCompliance::with('master')->findOrFail($id);

        // URUTAN PENTING: Set Class dulu agar list 'ExistingName' tersedia
        $this->compliance_class = $data->master->class;
        $this->compliance_name = $data->master->name;

        // Sesuaikan format tanggal untuk Flatpickr (d-m-Y)
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

    public function render()
    {
        return view('livewire.administration.people.compliance', [
            'compliances' => ModelsCompliance::where('user_id', $this->userId)
                ->with('master')
                ->latest() // Menampilkan data terbaru di atas
                ->get()
        ]);
    }
}
