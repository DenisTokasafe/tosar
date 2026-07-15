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

    // --- PROPERTI TAMBAHAN UNTUK FITUR PENYAKIT BARU ---
    public $showAddDiseaseModal = false;
    public $new_disease_name;

    public function openReviewModal($id)
    {
        $this->resetValidation();
        $this->reset(['fit_status', 'restriction_notes', 'follow_up_date', 'doctor_notes', 'selectedDiseaseCategories']);

        $this->selectedResultId = $id;

        // Load kategori jika sudah pernah diset sebelumnya
        $result = McuResult::with('diseaseCategories')->find($id);
        if ($result) {
            $this->selectedDiseaseCategories = $result->diseaseCategories->pluck('id')->toArray();
        }

        $this->showReviewModal = true;
    }

    // --- FUNGSI MEMBUKA & MENUTUP MODAL DAISYUI ---
    public function openAddDiseaseModal()
    {
        $this->resetValidation('new_disease_name');
        $this->new_disease_name = '';
        $this->showAddDiseaseModal = true;
    }

    public function closeAddDiseaseModal()
    {
        $this->showAddDiseaseModal = false;
        $this->new_disease_name = '';
    }

    // --- FUNGSI SIMPAN PENYAKIT BARU & OTOMATIS CENTANG ---
    public function saveNewDisease()
    {
        $this->validate([
            'new_disease_name' => 'required|string|min:3|unique:disease_categories,name',
        ], [
            'new_disease_name.required' => 'Nama penyakit wajib diisi.',
            'new_disease_name.unique' => 'Penyakit ini sudah ada di dalam daftar.',
            'new_disease_name.min' => 'Nama penyakit minimal 3 karakter.',
        ]);

        // 1. Simpan ke master database
        $newCategory = DiseaseCategory::create([
            'name' => trim($this->new_disease_name)
        ]);

        // 2. Otomatis tambahkan ID baru ke dalam array checkbox yang sedang terpilih
        $this->selectedDiseaseCategories[] = (string) $newCategory->id;

        // 3. Tutup modal dan reset input
        $this->closeAddDiseaseModal();
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
