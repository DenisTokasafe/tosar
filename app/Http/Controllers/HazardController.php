<?php

namespace App\Http\Controllers;

use App\Models\Hazard;
use Illuminate\Http\Request;

class HazardController extends Controller
{
    public function getExcelData()
{
    // Ambil data Hazard dengan semua relasi yang ingin ditampilkan di Excel
    $hazards = Hazard::with([
        'eventType',
        'eventSubType',
        'department',
        'contractor',
        'penanggungJawab',
        'pelapor',
        'location',
        'consequence',
        'likelihood'
    ])->get();

    // Mapping data agar lebih ringkas dan mudah dibaca (flattened structure)
    $data = $hazards->map(function ($hazard) {
        return [
            'ID' => $hazard->id,
            'No_Referensi' => $hazard->no_referensi,
            'Tanggal_Kejadian' => $hazard->tanggal,
            'Status' => $hazard->status,
            'Tipe_Event' => $hazard->eventType->event_type_name ?? 'N/A',
            'Sub_Tipe_Event' => $hazard->eventSubType->event_sub_type_name ?? 'N/A',
            'Departemen' => $hazard->department->department_name ?? 'N/A',
            'Kontraktor' => $hazard->contractor->contractor_name ?? 'N/A',
            'Pelapor_Nama' => $hazard->pelapor->name ?? $hazard->manualPelaporName,
            'Deskripsi_Hazard' => $hazard->description,
            'Tingkat_Risiko' => $hazard->risk_level,
            'Penanggung_Jawab' => $hazard->penanggungJawab->name ?? 'N/A',
            'Lokasi' => $hazard->location->location_name ?? 'N/A',
            'Konsekuensi' => $hazard->consequence->consequence_name ?? 'N/A',
            'Kemungkinan' => $hazard->likelihood->likelihood_name ?? 'N/A',
            'lokasi_spesifik' => $hazard->location_specific,
            'tindakan_korektif_segera' => $hazard->immediate_corrective_action,
            'kata_kunci' => $hazard->key_word,
            'kondisi_tidak_aman_id' => $hazard->hazardKondisiTidakAman() ? $hazard->hazardKondisiTidakAman->name : null,
            'tindakan_tidak_aman_id' => $hazard->hazardTindakanTidakAman() ? $hazard->hazardTindakanTidakAman->name : null,
            // ... tambahkan semua kolom yang Anda butuhkan
        ];
    });

    // Kembalikan data dalam format JSON
    return response()->json($data);
}
}
