<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IncidentRisk extends Model
{
    use LogsActivity; // Aktifkan pencatatan aktivitas

    protected $fillable = [
        'incident_report_id',
        'likelihood_id',
        'consequence_id',
        'rating_name',
        'deadline'
    ];

    /**
     * Konfigurasi Log Activity untuk Penilaian Risiko
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()           // Mencatat perubahan likelihood, consequence, rating, dan deadline
            ->logOnlyDirty()          // Hanya catat jika ada perubahan nilai
            ->dontSubmitEmptyLogs()
            ->useLogName('IncidentDetail') // Kelompokkan dengan relasi investigasi lainnya
            ->setDescriptionForEvent(fn(string $eventName) => "Incident Risk Assessment has been {$eventName}");
    }

    /**
     * Relasi balik ke Report
     */
    public function report()
    {
        return $this->belongsTo(IncidentReport::class, 'incident_report_id');
    }

    // Rekomendasi: Tambahkan relasi ke Likelihood & Consequence agar bisa ditarik namanya di Audit Trail
    public function likelihood()
    {
        return $this->belongsTo(Likelihood::class);
    }

    public function consequence()
    {
        return $this->belongsTo(RiskConsequence::class);
    }
}
