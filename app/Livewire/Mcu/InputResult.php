<?php

namespace App\Livewire\Mcu;

use App\Models\McuParticipant;
use App\Models\McuResult; // Pastikan Anda sudah membuat Model ini
use Livewire\Component;
use Livewire\WithFileUploads;

class InputResult extends Component
{
    use WithFileUploads;

    public $participant_id;
    public $result_document;
    public $admin_notes;

    public function rules()
    {
        return [
            'participant_id' => 'required|exists:mcu_participants,id',
            'result_document' => 'required|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'admin_notes' => 'nullable|string',
        ];
    }

    public function saveResult()
    {
        $this->validate();

        // Upload File
        $path = $this->result_document->store('mcu_results', 'public');

        // Simpan ke database dengan status langsung 'pending_review' (Sesuai flowchart)
        McuResult::create([
            'mcu_participant_id' => $this->participant_id,
            'result_document' => $path,
            'admin_notes' => $this->admin_notes,
            'status' => 'pending_review'
        ]);

        session()->flash('message', 'Hasil MCU berhasil diunggah. Menunggu review Dokter.');
        $this->reset(['participant_id', 'result_document', 'admin_notes']);
    }

    public function render()
    {
        // Ambil data peserta yang belum memiliki hasil MCU
        $participants = McuParticipant::whereDoesntHave('result')->with('employee', 'schedule')->get();

        return view('livewire.mcu.input-result', [
            'participants' => $participants
        ]);
    }
}
