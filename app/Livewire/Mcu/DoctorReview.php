<?php

namespace App\Livewire\Mcu;

use App\Models\McuResult;
use App\Models\DiseaseCategory;
use App\Notifications\McuResultNotification;
use Livewire\Component;

class DoctorReview extends Component
{
    public $selectedResultId;
    public $fit_status;
    public $restriction_notes;
    public $follow_up_date;
    public $doctor_notes;

    public $selectedDiseaseCategories = [];
    public $showReviewModal = false;

    // Properti untuk input inline
    public $new_disease_name;

    public function openReviewModal($id)
    {
        $this->resetValidation();
        $this->reset(['fit_status', 'restriction_notes', 'follow_up_date', 'doctor_notes', 'selectedDiseaseCategories', 'new_disease_name']);

        $this->selectedResultId = $id;

        $result = McuResult::with('diseaseCategories')->find($id);
        if ($result) {
            $this->selectedDiseaseCategories = $result->diseaseCategories->pluck('id')->toArray();
        }

        $this->showReviewModal = true;
    }

    // --- FUNGSI SIMPAN PENYAKIT DARI INPUT INLINE ---
    public function saveNewDisease()
    {
        $this->validate([
            'new_disease_name' => 'required|string|min:3|unique:disease_categories,name',
        ], [
            'new_disease_name.required' => 'Nama wajib diisi.',
            'new_disease_name.unique' => 'Penyakit sudah ada.',
            'new_disease_name.min' => 'Min. 3 huruf.',
        ]);

        // 1. Simpan ke database
        $newCategory = DiseaseCategory::create([
            'name' => trim($this->new_disease_name)
        ]);

        // 2. Otomatis centang penyakit baru tersebut
        $this->selectedDiseaseCategories[] = (string) $newCategory->id;

        // 3. Kosongkan kembali kolom input agar siap dipakai mengetik lagi jika perlu
        $this->new_disease_name = '';
        $this->resetValidation('new_disease_name');
    }

    public function saveReview()
    {
        $this->validate([
            'fit_status'                => 'required|in:fit_to_work,fit_with_notes,temporary_unfit,unfit',
            'restriction_notes'         => 'required_if:fit_status,fit_with_notes|nullable|string',
            'follow_up_date'            => 'required_if:fit_status,temporary_unfit|nullable|date',
            'doctor_notes'              => 'nullable|string',
            'selectedDiseaseCategories'   => 'nullable|array',
            'selectedDiseaseCategories.*' => 'exists:disease_categories,id',
        ], [
            'restriction_notes.required_if' => 'Catatan batasan kerja wajib diisi untuk status ini.',
            'follow_up_date.required_if'    => 'Tanggal Follow Up wajib diisi untuk status ini.'
        ]);

        if (!$this->selectedResultId) {
            session()->flash('error', 'Data MCU tidak ditemukan.');
            return;
        }

        $result = McuResult::with(['participant.employee', 'participant.deptHead'])->find($this->selectedResultId);

        if ($result) {
            $result->update([
                'status'              => $this->fit_status,
                'workflow_status'     => 'reviewed',
                'doctor_site_consult' => $this->fit_status === 'fit_with_notes' ? $this->restriction_notes : null,
                'follow_up_date'      => $this->fit_status === 'temporary_unfit' ? $this->follow_up_date : null,
                'doctor_notes'        => $this->doctor_notes,
                'reviewed_by'         => auth()->id(),
                'reviewed_at'         => now(),
            ]);

            // Sync penyakit temuan ke database
            $result->diseaseCategories()->sync($this->selectedDiseaseCategories);

            $employeeUser = $result->participant?->employee;
            $deptHeadUser = $result->participant?->deptHead;

            if ($employeeUser) {
                $employeeUser->notify(new McuResultNotification($result, 'employee'));
            }

            if ($deptHeadUser) {
                $deptHeadUser->notify(new McuResultNotification($result, 'dept_head'));
            }

            $this->showReviewModal = false;
            $this->reset(['fit_status', 'restriction_notes', 'follow_up_date', 'doctor_notes', 'selectedDiseaseCategories', 'selectedResultId']);

            session()->flash('message', 'Review MCU berhasil disimpan dan notifikasi hasil telah terkirim.');
        }
    }

    public function render()
    {
        $pendingReviews = McuResult::where('workflow_status', 'pending_doctor')
            ->with(['participant.employee', 'diseaseCategories'])
            ->get();

        return view('livewire.mcu.doctor-review', [
            'pendingReviews'    => $pendingReviews,
            'diseaseCategories' => DiseaseCategory::orderBy('name')->get(),
        ]);
    }
}
