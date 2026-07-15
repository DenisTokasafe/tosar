<?php

namespace App\Livewire\Mcu;

use App\Models\McuResult;
use App\Models\DiseaseCategory; // 1. Import model DiseaseCategory
use App\Notifications\McuResultNotification;
use Livewire\Component;

class DoctorReview extends Component
{
    public $selectedResultId;
    public $fit_status; // Akan menyimpan nilai: fit_to_work, fit_with_notes, temporary_unfit, unfit
    public $restriction_notes;
    public $follow_up_date;
    public $doctor_notes;

    // 2. Tambahkan properti array untuk menampung ID kategori penyakit yang dicentang
    public $selectedDiseaseCategories = [];

    public $showReviewModal = false;

    public function openReviewModal($id)
    {
        $this->resetValidation();
        // 3. Reset juga array kategori penyakit
        $this->reset(['fit_status', 'restriction_notes', 'follow_up_date', 'doctor_notes', 'selectedDiseaseCategories']);

        $this->selectedResultId = $id;

        // 4. (Opsional tapi direkomendasikan) Load data penyakit jika sebelumnya dokter sudah pernah menyimpan/draft
        $result = McuResult::with('diseaseCategories')->find($id);
        if ($result) {
            $this->selectedDiseaseCategories = $result->diseaseCategories->pluck('id')->toArray();
        }

        $this->showReviewModal = true;
    }

    public function saveReview()
    {
        $this->validate([
            'fit_status'                => 'required|in:fit_to_work,fit_with_notes,temporary_unfit,unfit',
            'restriction_notes'         => 'required_if:fit_status,fit_with_notes|nullable|string',
            'follow_up_date'            => 'required_if:fit_status,temporary_unfit|nullable|date',
            'doctor_notes'              => 'nullable|string',

            // 5. Validasi untuk kategori penyakit
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

        // Pastikan memuat relasi supervisor
        $result = McuResult::with(['participant.employee', 'participant.deptHead'])->find($this->selectedResultId);

        if ($result) {
            // 1. Jalankan Update ke Database
            $result->update([
                'status'              => $this->fit_status,
                'workflow_status'     => 'reviewed',
                'doctor_site_consult' => $this->fit_status === 'fit_with_notes' ? $this->restriction_notes : null,
                'follow_up_date'      => $this->fit_status === 'temporary_unfit' ? $this->follow_up_date : null,
                'doctor_notes'        => $this->doctor_notes,
                'reviewed_by'         => auth()->id(),
                'reviewed_at'         => now(),
            ]);

            // 6. Simpan relasi Many-to-Many menggunakan sync()
            // sync() otomatis menambahkan ID baru dan menghapus ID yang tidak dicentang di tabel pivot
            $result->diseaseCategories()->sync($this->selectedDiseaseCategories);

            // 2. Ambil Model User Terkait
            $employeeUser = $result->participant?->employee;
            $deptHeadUser = $result->participant?->deptHead;

            // 3. Kirim Notifikasi ke Karyawan
            if ($employeeUser) {
                $employeeUser->notify(new McuResultNotification($result, 'employee'));
            }

            // 4. Kirim Notifikasi ke Dept Head / Supervisor
            if ($deptHeadUser) {
                $deptHeadUser->notify(new McuResultNotification($result, 'dept_head'));
            }

            // 5. Tutup Modal & Reset Form
            $this->showReviewModal = false;
            $this->reset(['fit_status', 'restriction_notes', 'follow_up_date', 'doctor_notes', 'selectedDiseaseCategories', 'selectedResultId']);

            session()->flash('message', 'Review MCU berhasil disimpan dan notifikasi hasil telah terkirim.');
        }
    }

    public function render()
    {
        // Tampilkan hanya yang butuh direview oleh dokter
        $pendingReviews = McuResult::where('workflow_status', 'pending_doctor')
            ->with(['participant.employee', 'diseaseCategories']) // Tambahkan eager load diseaseCategories jika ingin ditampilkan di tabel
            ->get();

        return view('livewire.mcu.doctor-review', [
            'pendingReviews'    => $pendingReviews,
            'diseaseCategories' => DiseaseCategory::orderBy('name')->get(), // 7. Kirim master data penyakit ke Blade
        ]);
    }
}
