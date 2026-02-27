<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyPart extends Model
{


public function incidents()
{
    return $this->belongsToMany(Incident::class, 'incident_body_parts');
}
}
