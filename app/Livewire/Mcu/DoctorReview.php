<?php

namespace App\Livewire\Mcu;

use App\Models\McuResult;
use Livewire\Component;

class DoctorReview extends Component
{
    public $selectedResultId;
    public $fit_status; // Akan menyimpan nilai: fit_to_work, fit_with_notes, temporary_unfit, unfit
    public $restriction_notes;
    public $follow_up_date;
    public $doctor_notes;

    public $showReviewModal = false;

    public function openReviewModal($id)
    {
        $this->resetValidation();
        $this->reset(['fit_status', 'restriction_notes', 'follow_up_date', 'doctor_notes']);

        $this->selectedResultId = $id;
        $this->showReviewModal = true;
    }

    public function saveReview()
    {
        // 1. Validasi disesuaikan dengan nilai ENUM database (lowercase snake_case)
        $this->validate([
            'fit_status'        => 'required|in:fit_to_work,fit_with_notes,temporary_unfit,unfit',
            'restriction_notes' => 'required_if:fit_status,fit_with_notes',
            'follow_up_date'    => 'required_if:fit_status,temporary_unfit|date',
            'doctor_notes'      => 'nullable|string'
        ], [
            'restriction_notes.required_if' => 'Catatan batasan kerja wajib diisi untuk status ini.',
            'follow_up_date.required_if'    => 'Tanggal Follow Up wajib diisi untuk status ini.'
        ]);

        $result = McuResult::find($this->selectedResultId);

        // 2. Mapping data ke kolom database yang benar
        $result->update([
            'status'              => $this->fit_status, // Mengisi kolom status medis
            'workflow_status'     => 'reviewed',       // Mengubah status alur kerja
            'doctor_site_consult' => $this->fit_status === 'fit_with_notes' ? $this->restriction_notes : null, // Simpan ke doctor_site_consult
            'follow_up_date'      => $this->fit_status === 'temporary_unfit' ? $this->follow_up_date : null,
            'doctor_notes'        => $this->doctor_notes,
            'reviewed_by'         => auth()->id(),
            'reviewed_at'         => now(),
        ]);

        $this->showReviewModal = false;
        session()->flash('message', 'Review MCU berhasil disimpan.');
    }

    public function render()
    {
        // Tampilkan hanya yang butuh direview oleh dokter
        $pendingReviews = McuResult::where('workflow_status', 'pending_doctor')
            ->with('participant.employee')
            ->get();

        return view('livewire.mcu.doctor-review', [
            'pendingReviews' => $pendingReviews
        ]);
    }
}
