<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IncidentImpact extends Model
{
    use LogsActivity; // Tambahkan Trait ini

    protected $guarded = ['id'];

    /**
     * Konfigurasi Logging untuk Dampak Insiden
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Catat semua kolom (injury_type, body_part_id, damage_detail, dll)
            ->logOnlyDirty()          // Hanya catat jika ada perubahan nilai
            ->dontSubmitEmptyLogs()
            ->useLogName('IncidentDetail')
            ->setDescriptionForEvent(fn(string $eventName) => "Incident Impact has been {$eventName}");
    }

    /**
     * Relasi ke tabel body_parts (Mata, Tangan, dll)
     */
    public function bodyPart(): BelongsTo
    {
        return $this->belongsTo(BodyPart::class, 'body_part_id');
    }

    /**
     * Relasi ke laporan induk
     */
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }
}
