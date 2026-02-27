<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incident extends Model
{

    use HasFactory;

    protected $fillable = [
        'reference_number',
        'penanggung_jawab_id',
        'pelapor_id',
        'manualPelaporName',
        'incident_datetime',
        'location_id',
        'location_specific',
        'event_type_id',
        'event_sub_type_id',
        'key_word',
        'kondisi_tidak_aman_id',
        'tindakan_tidak_aman_id',
        'description',
        'doc_deskripsi',
        'immediate_corrective_action',
        'doc_corrective',
        'consequence_id',
        'likelihood_id',
        'risk_level',
        'unit_involved',
        'status'
    ];

    protected $casts = [
        'incident_datetime' => 'datetime',
        // Jika Anda menggunakan Enum PHP untuk status/keyword (Sangat disarankan di Laravel 12)
        // 'status' => \App\Enums\IncidentStatus::class,
    ];

    // --- RELASI USER & PELAPOR ---

    public function penanggungJawab(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // --- RELASI LOKASI & TIPE ---

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function eventSubType(): BelongsTo
    {
        return $this->belongsTo(EventSubType::class);
    }

    // --- RELASI K3 (KTA/TTA) ---

    public function unsafeCondition(): BelongsTo
    {
        return $this->belongsTo(UnsafeCondition::class, 'kondisi_tidak_aman_id');
    }

    public function unsafeAct(): BelongsTo
    {
        return $this->belongsTo(UnsafeAct::class, 'tindakan_tidak_aman_id');
    }

    // --- RELASI RISK MATRIX ---

    public function consequence(): BelongsTo
    {
        return $this->belongsTo(RiskConsequence::class);
    }

    public function likelihood(): BelongsTo
    {
        return $this->belongsTo(Likelihood::class);
    }

    // --- RELASI BANYAK BAGIAN TUBUH (MANY-TO-MANY) ---

    public function bodyParts(): BelongsToMany
    {
        return $this->belongsToMany(BodyPart::class, 'incident_body_parts', 'incident_id', 'body_part_id')
            ->withTimestamps();
    }
}
