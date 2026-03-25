<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IncidentReport extends Model
{
    use HasFactory;

    // Mengizinkan mass-assignment untuk semua field kecuali ID
    protected $guarded = ['id'];

    // Casting tipe data agar otomatis menjadi objek Carbon atau tipe yang sesuai
    protected $casts = [
        'scat_analysis' => 'array',
        'date_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected static function booted()
    {
        static::saving(function ($incident) {
            // 1. Initial Reporting (Step 1 & 2) -> Default
            $status = 'Open';

            // Cek kelengkapan untuk Tahap Investigation (Step 3-6)
            $hasInvestigation = $incident->investigationTeams()->exists() && $incident->timelines()->exists();

            // Cek kelengkapan untuk Action Plan (Step 7-8)
            $hasActionPlan = $incident->correctiveActions()->exists() &&
                !empty($incident->key_learning);

            // Cek kelengkapan untuk Final Review (Step 9)
            $isAllCorrectiveFinished = $incident->correctiveActions()->count() > 0 &&
                !$incident->correctiveActions()->whereNull('actual_completion_date')->exists();

            $hasFinalComments = !empty($incident->penerimaan_komentar_ohs) &&
                !empty($incident->penerimaan_komentar_internal);

            // LOGIKA PENENTUAN STATUS
            if ($hasInvestigation) {
                $status = 'In Progress';
            }

            if ($hasActionPlan) {
                $status = 'Action Required';
            }

            if ($isAllCorrectiveFinished && $hasFinalComments) {
                // Jika KTT diperlukan (Konsekuensi 3-5), cek juga otoritas KTT
                if (in_array((int)$incident->consequence_id, [3, 4, 5])) {
                    if (!empty($incident->penerimaan_komentar_ktt)) {
                        $status = 'Closed';
                    }
                } else {
                    $status = 'Closed';
                }
            }

            $incident->status = $status;
        });
    }

    /**
     * ==========================================
     * RELASI HEADER (BELONGS TO)
     * ==========================================
     */

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function eventSubType(): BelongsTo
    {
        return $this->belongsTo(EventSubType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function pic(): BelongsTo // Penanggung Jawab
    {
        return $this->belongsTo(User::class, 'penanggungJawab');
    }

    public function reporter(): BelongsTo // Pelapor
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    /**
     * ==========================================
     * RELASI APPROVAL (PART 9)
     * ==========================================
     */

    public function pmContractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pm_contractor_id');
    }

    public function pmInternal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pm_internal_id');
    }

    public function ohsHead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ohs_head_id');
    }

    public function ktt(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ktt_id');
    }

    /**
     * ==========================================
     * RELASI DETAIL (HAS ONE / HAS MANY)
     * ==========================================
     */

    // Part 2 & Body Parts: Dampak Insiden (Injury/Damage)
    public function impact(): HasOne
    {
        return $this->hasOne(IncidentImpact::class);
    }

    // Part 2: Personil Terlibat
    public function involvedPersons(): HasMany
    {
        return $this->hasMany(InvolvedPerson::class);
    }

    // Part 3: Tim Investigasi
    public function investigationTeams(): HasMany
    {
        return $this->hasMany(InvestigationTeam::class);
    }

    // Part 4: Analisis PEEPO
    public function peepoAnalyses(): HasMany
    {
        return $this->hasMany(PeepoAnalysis::class);
    }

    // Part 5: Timeline & 5 Whys
    public function timelines(): HasMany
    {
        return $this->hasMany(TimelineAnalysis::class);
    }

    // Part 7: Tindakan Perbaikan
    public function correctiveActions(): HasMany
    {
        return $this->hasMany(CorrectiveAction::class);
    }

    // Part 7: Upload Gambar/Dokumen
    public function attachments(): HasMany
    {
        return $this->hasMany(IncidentAttachment::class);
    }
    public function risk()
    {
        // HasOne karena 1 laporan hanya punya 1 penilaian risiko
        return $this->hasOne(IncidentRisk::class, 'incident_report_id');
    }

    public function getManagerReviewerAttribute()
    {
        // Mengambil user yang memiliki role Manager/Superintendent di departemen terkait
        // Atau ambil dari data yang sudah di-assign di Part 9
        return $this->reviews()->first()?->user?->name ?? 'Belum Ditentukan';
    }
    /**
     * Mendapatkan peninjau terakhir yang mengisi komentar untuk Summary Widget
     */
    /**
     * Mendapatkan peninjau terakhir yang mengisi komentar untuk Summary Widget
     */
    public function getLatestReviewerNameAttribute()
    {
        // Gunakan optional() atau null safe operator untuk menghindari error jika relasi kosong
        if ($this->ktt_id) return $this->ktt?->name;
        if ($this->ohs_head_id) return $this->ohsHead?->name;
        if ($this->pm_internal_id) return $this->pmInternal?->name;
        if ($this->pm_contractor_id) return $this->pmContractor?->name;

        return 'Waiting Review';
    }
    public function getLatestReviewerRoleAttribute()
    {
        if ($this->ktt_id) return 'KTT';
        if ($this->ohs_head_id) return 'OHS Head';
        if ($this->pm_internal_id) return 'PM Internal';
        if ($this->pm_contractor_id) return 'PM Contractor';

        return 'Pending';
    }
}
