<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CorrectiveAction extends Model
{
    use LogsActivity; // Aktifkan Trait logging

    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'date',
        'actual_completion_date' => 'date',
    ];

    /**
     * Konfigurasi Logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Mencatat semua field (termasuk incident_report_id)
            ->logOnlyDirty()          // Hanya catat jika ada perubahan data
            ->dontSubmitEmptyLogs()   // Jangan simpan log jika tidak ada yang berubah
            ->useLogName('IncidentDetail') // Nama kategori untuk filter di Audit Trail
            ->setDescriptionForEvent(fn(string $eventName) => "Corrective Action has been {$eventName}");
    }

    /**
     * Relasi Induk
     */
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }

    /**
     * Relasi ke User untuk mengetahui siapa PIC-nya
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }
}
