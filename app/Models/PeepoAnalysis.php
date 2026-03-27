<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PeepoAnalysis extends Model
{
    use LogsActivity; // Aktifkan pencatatan aktivitas

    protected $guarded = ['id'];

    /**
     * Konfigurasi Log Activity untuk Analisis PEEPO
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Mencatat kolom category, description, incident_report_id, dll
            ->logOnlyDirty()          // Hanya catat jika ada perubahan pada data analisis
            ->dontSubmitEmptyLogs()
            ->useLogName('IncidentDetail') // Kelompokkan dengan relasi investigasi lainnya
            ->setDescriptionForEvent(fn(string $eventName) => "PEEPO Analysis has been {$eventName}");
    }

    /**
     * Relasi balik ke laporan utama
     */
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }
}
