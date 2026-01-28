<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentMaster extends Model
{
   protected $fillable = ['type', 'location_id', 'specific_location', 'technical_data', 'is_active'];

    protected $casts = [
        'technical_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
