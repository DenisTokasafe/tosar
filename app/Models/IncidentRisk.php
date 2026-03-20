<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentRisk extends Model
{
    protected $fillable = [
        'incident_report_id',
        'likelihood_id',
        'consequence_id',
        'rating_name',
        'deadline'
    ];

    // Relasi balik ke Report (Opsional)
    public function report()
    {
        return $this->belongsTo(IncidentReport::class, 'incident_report_id');
    }
}
