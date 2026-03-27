<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class InvolvedPerson extends Model
{
    use LogsActivity; // Aktifkan logging aktivitas

    protected $guarded = ['id'];
    protected $table = 'involved_persons';

    /**
     * Konfigurasi Log Activity
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Mencatat semua kolom (nik, nama, jabatan, perusahaan, dll)
            ->logOnlyDirty()          // Hanya catat kolom yang nilainya benar-benar berubah
            ->dontSubmitEmptyLogs()
            ->useLogName('IncidentDetail') // Kelompokkan dalam kategori Detail agar rapi di Modal
            ->setDescriptionForEvent(fn(string $eventName) => "Involved Person has been {$eventName}");
    }

    /**
     * Relasi ke laporan induk
     */
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }
}
