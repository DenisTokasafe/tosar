<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

class IncidentReport extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'scat_analysis' => 'array',
        'date_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Konfigurasi Log Activity
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Incident');
    }

    /**
     * Tap Activity: Mengubah ID menjadi Label Nama agar log mudah dibaca
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $properties = $activity->properties->toArray();

        $map = [
            'event_type_id'     => fn() => $this->eventType?->event_type_name,
            'event_sub_type_id' => fn() => $this->eventSubType?->event_sub_type_name,
            'location_id'       => fn() => $this->location?->name,
            'department_id'     => fn() => $this->department?->department_name,
            'contractor_id'     => fn() => $this->contractor?->contractor_name,
            'pelapor_id'        => fn() => $this->reporter?->name,
            'penanggung_jawab'  => fn() => $this->pic?->name,
            'pm_contractor_id'  => fn() => $this->pmContractor?->name,
            'pm_internal_id'    => fn() => $this->pmInternal?->name,
            'ohs_head_id'       => fn() => $this->ohsHead?->name,
            'ktt_id'            => fn() => $this->ktt?->name,
        ];

        foreach (['attributes', 'old'] as $key) {
            if (!isset($properties[$key])) continue;

            foreach ($map as $field => $resolver) {
                if (isset($properties[$key][$field])) {
                    $properties[$key][$field . '_label'] = $resolver();
                }
            }
        }

        $activity->properties = collect($properties);
    }

    /**
     * RELASI AKTIVITAS
     */

    // Relasi standar (Hanya log milik IncidentReport itu sendiri)
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    // Relasi kustom (Menggabungkan log header + log milik tabel anak)
    public function allActivities()
    {
        return Activity::where(function ($query) {
            $query->where('subject_type', IncidentReport::class)
                ->where('subject_id', $this->id);
        })->orWhere(function ($query) {
            $query->where('properties->attributes->incident_report_id', $this->id)
                ->orWhere('properties->old->incident_report_id', $this->id);
        })->latest();
    }

    /**
     * ==========================================
     * RELASI HEADER (BELONGS TO)
     * ==========================================
     */

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
    public function eventSubType(): BelongsTo
    {
        return $this->belongsTo(EventSubType::class, 'event_sub_type_id');
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
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penanggung_jawab');
    }
    public function reporter(): BelongsTo
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
     * RELASI DETAIL (HAS MANY / HAS ONE)
     * ==========================================
     */

    public function impact(): HasOne
    {
        return $this->hasOne(IncidentImpact::class);
    }
    public function involvedPersons(): HasMany
    {
        return $this->hasMany(InvolvedPerson::class);
    }
    public function investigationTeams(): HasMany
    {
        return $this->hasMany(InvestigationTeam::class);
    }
    public function peepoAnalyses(): HasMany
    {
        return $this->hasMany(PeepoAnalysis::class);
    }
    public function timelines(): HasMany
    {
        return $this->hasMany(TimelineAnalysis::class, 'incident_report_id');
    }
    public function correctiveActions(): HasMany
    {
        return $this->hasMany(CorrectiveAction::class);
    }
    public function attachments(): HasMany
    {
        return $this->hasMany(IncidentAttachment::class);
    }
    public function risk(): HasOne
    {
        return $this->hasOne(IncidentRisk::class, 'incident_report_id');
    }

    /**
     * ==========================================
     * ACCESSORS
     * ==========================================
     */

    public function getLatestReviewerNameAttribute()
    {
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
