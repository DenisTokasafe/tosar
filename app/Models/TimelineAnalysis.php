<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TimelineAnalysis extends Model
{
    use LogsActivity; // Aktifkan pencatatan aktivitas

    protected $guarded = ['id'];

    // Penting: Cast agar array otomatis jadi JSON di DB dan sebaliknya
    protected $casts = [
        'analysis_steps' => 'array',
    ];

    /**
     * Konfigurasi Log Activity untuk Timeline & 5 Whys
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Mencatat event_description, analysis_steps, dll
            ->logOnlyDirty()          // Hanya catat jika ada perubahan isi timeline
            ->dontSubmitEmptyLogs()
            ->useLogName('IncidentDetail') // Kelompokkan dengan relasi investigasi lainnya
            ->setDescriptionForEvent(fn(string $eventName) => "Timeline Analysis has been {$eventName}");
    }

    /**
     * Relasi balik ke laporan utama
     */
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }
}
