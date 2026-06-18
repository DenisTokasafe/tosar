<?php

namespace App\Livewire\Mcu;

use App\Models\McuResult;
use Livewire\Component;

class DoctorReview extends Component
{
    public $selectedResultId;
    public $fit_status;
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
        $this->validate([
            'fit_status' => 'required|in:Fit To Work,Fit With Restriction,Temporary Unfit,Permanent Unfit',
            'restriction_notes' => 'required_if:fit_status,Fit With Restriction',
            'follow_up_date' => 'required_if:fit_status,Temporary Unfit|date',
            'doctor_notes' => 'nullable|string'
        ], [
            'restriction_notes.required_if' => 'Catatan batasan kerja wajib diisi untuk status ini.',
            'follow_up_date.required_if' => 'Tanggal Follow Up wajib diisi untuk status ini.'
        ]);

        $result = McuResult::find($this->selectedResultId);
        $result->update([
            'fit_status' => $this->fit_status,
            'restriction_notes' => $this->fit_status === 'Fit With Restriction' ? $this->restriction_notes : null,
            'follow_up_date' => $this->fit_status === 'Temporary Unfit' ? $this->follow_up_date : null,
            'doctor_notes' => $this->doctor_notes,
            'status' => 'reviewed', // Status berubah menjadi reviewed (siap di-publish nanti)
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $this->showReviewModal = false;
        session()->flash('message', 'Review MCU berhasil disimpan.');
    }

    public function render()
    {
        // Tampilkan hanya yang butuh direview
        $pendingReviews = McuResult::where('status', 'pending_review')
            ->with('participant.employee')
            ->get();

        return view('livewire.mcu.doctor-review', [
            'pendingReviews' => $pendingReviews
        ]);
    }
}
