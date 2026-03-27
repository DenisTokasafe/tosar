<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IncidentAttachment extends Model
{
    use LogsActivity; // Aktifkan pencatatan aktivitas

    protected $guarded = ['id'];

    /**
     * Konfigurasi Log Activity untuk Lampiran/Dokumen
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Mencatat file_path, file_name, file_type, dll
            ->logOnlyDirty()          // Hanya catat jika ada perubahan (misal: ganti nama file)
            ->dontSubmitEmptyLogs()
            ->useLogName('IncidentDetail') // Kelompokkan dengan relasi investigasi lainnya
            ->setDescriptionForEvent(fn(string $eventName) => "Incident Attachment has been {$eventName}");
    }

    /**
     * Relasi balik ke laporan utama
     */
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }
}
