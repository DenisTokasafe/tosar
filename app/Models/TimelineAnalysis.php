<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineAnalysis extends Model
{
    protected $guarded = ['id'];

    // Penting: Cast agar array otomatis jadi JSON di DB dan sebaliknya
    protected $casts = [
        'analysis_steps' => 'array',
    ];

    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }
}
