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
        // 1. TAMBAHKAN 'nullable' pada restriction_notes dan follow_up_date
        $this->validate([
            'fit_status'        => 'required|in:fit_to_work,fit_with_notes,temporary_unfit,unfit',
            'restriction_notes' => 'required_if:fit_status,fit_with_notes|nullable|string',
            'follow_up_date'    => 'required_if:fit_status,temporary_unfit|nullable|date',
            'doctor_notes'      => 'nullable|string'
        ], [
            'restriction_notes.required_if' => 'Catatan batasan kerja wajib diisi untuk status ini.',
            'follow_up_date.required_if'    => 'Tanggal Follow Up wajib diisi untuk status ini.'
        ]);

        // Pastikan ID ini tidak null sebelum melakukan query
        if (!$this->selectedResultId) {
            session()->flash('error', 'Data MCU tidak ditemukan.');
            return;
        }

        $result = McuResult::find($this->selectedResultId);

        if ($result) {
            // 2. Mapping data ke kolom database
            $result->update([
                'status'              => $this->fit_status,
                'workflow_status'     => 'reviewed',
                'doctor_site_consult' => $this->fit_status === 'fit_with_notes' ? $this->restriction_notes : null,
                'follow_up_date'      => $this->fit_status === 'temporary_unfit' ? $this->follow_up_date : null,
                'doctor_notes'        => $this->doctor_notes,
                'reviewed_by'         => auth()->id(),
                'reviewed_at'         => now(),
            ]);

            $this->showReviewModal = false;

            // Reset form setelah berhasil disimpan agar bersih kembali
            $this->reset(['fit_status', 'restriction_notes', 'follow_up_date', 'doctor_notes', 'selectedResultId']);

            session()->flash('message', 'Review MCU berhasil disimpan.');
        }
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
