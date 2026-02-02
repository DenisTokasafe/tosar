<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FireProtection extends Model
{
    protected $fillable = [
        'equipment_master_id',
        'inspection_date',
        'inspected_by',
        'conditions',
        'remarks',
        'documentation_path',
    ];
    protected $casts = [
    'conditions' => 'array',
    'inspected_by' => 'array', // Karena Anda menggunakan pembatas '|'
    'inspection_date' => 'date',
];
    public function equipmentMaster()
    {
        return $this->belongsTo(EquipmentMaster::class);
    }
   public function scopeSearchInstectionsByDate($query, $date)
    {
        if ($date) {
            // Gunakan $query, bukan $this
            return $query->whereMonth('inspection_date', $date);
        }
        return $query;
    }
    public function scopeSearchByLocation($query, $locationId)
    {
        if ($locationId) {
            return $query->whereHas('equipmentMaster', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }
        return $query;
    }
}
