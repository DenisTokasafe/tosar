<?php

namespace App\Livewire\Mcu;

use App\Models\McuParticipant;
use App\Models\McuResult;
use Carbon\Carbon;
use Livewire\Component;

class McuResultList extends Component
{
    public $showReviewModal = false; // Flag untuk menampilkan/menyembunyikan modal
    public $selectedResultId;

    // Properti Form
    public $fit_status;
    public $restriction_notes;
    public $follow_up_date;
    public $doctor_notes;

    // Opsional: untuk ditampilkan sebagai judul di dalam modal
    public $employeeName;
    public function render()
    {
        $today = Carbon::today();
        $mcuResults = McuResult::with([
            'participant.employee',
            'participant.schedule'
        ])
            ->whereIn('workflow_status', ['pending_doctor', 'reviewed'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'resultPage'); // Tambahkan nama page agar paginasi tidak bentrok

        // 2. Query Data Tidak Hadir MCU (Tambahan Baru)
        $absentParticipants = McuParticipant::with(['employee', 'schedule'])
            ->whereDoesntHave('result') // Belum ada hasil MCU
            ->whereHas('schedule', function ($query) use ($today) {
                // Jadwalnya sudah lewat dari hari ini
                $query->whereDate('schedule_date', '<', $today);
            })
            ->paginate(10, ['*'], 'absentPage'); // Nama page khusus untuk tabel absen

        return view('livewire.mcu.mcu-result-list', [
            'mcuResults' => $mcuResults,
            'absentParticipants' => $absentParticipants // Kirim ke view
        ]);
    }

    public function openReviewModal($id)
    {
        // 1. Cari data MCU beserta relasi karyawannya
        $result = McuResult::with('participant.employee')->find($id);

        if ($result) {
            // 2. Set ID yang sedang dipilih
            $this->selectedResultId = $result->id;

            // 3. Isi field form dengan data yang ada di database (jika sebelumnya sudah ada isi)
            $this->fit_status        = $result->status;
            $this->restriction_notes = $result->doctor_site_consult;
            $this->doctor_notes      = $result->doctor_notes;

            // Format tanggal agar sesuai dengan input type="date" di HTML (Y-m-d)
            $this->follow_up_date    = $result->follow_up_date
                ? $result->follow_up_date->format('Y-m-d')
                : null;

            // Ambil nama karyawan untuk keperluan UI Modal
            $this->employeeName = $result->participant->employee->name ?? 'Tidak diketahui';

            // 4. Buka modal
            $this->showReviewModal = true;
        } else {
            session()->flash('error', 'Data hasil MCU tidak ditemukan.');
        }
    }
    public function closeReviewModal()
    {
        $this->showReviewModal = false;

        // Bersihkan data form agar tidak terbawa ke data orang lain saat membuka modal baru
        $this->reset([
            'selectedResultId',
            'fit_status',
            'restriction_notes',
            'follow_up_date',
            'doctor_notes',
            'employeeName'
        ]);
    }
    public function paginationView()
    {
        return 'paginate.pagination';
    }
}
