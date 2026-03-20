<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvolvedPerson extends Model
{

    protected $guarded = ['id'];
    protected $table = 'involved_persons';

    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }
}
