<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class InvestigationTeam extends Model
{
    use LogsActivity; // Aktifkan pencatatan aktivitas

    protected $guarded = ['id'];

    /**
     * Konfigurasi Log Activity untuk Tim Investigasi
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Mencatat user_id, role, incident_report_id, dll
            ->logOnlyDirty()          // Hanya catat jika ada perubahan data
            ->dontSubmitEmptyLogs()
            ->useLogName('IncidentDetail') // Kelompokkan dengan relasi lainnya
            ->setDescriptionForEvent(fn(string $eventName) => "Investigation Team member has been {$eventName}");
    }

    /**
     * Relasi balik ke laporan utama
     */
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }

    /**
     * Relasi ke tabel User untuk mengambil Nama, Foto Profil, dll.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
