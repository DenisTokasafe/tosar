<?php

namespace App\Models;

use Carbon\Carbon;
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
        'area_photo_path',
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
            $date = Carbon::parse($date);
            $currentMonth = $date->month; //
            // Gunakan $query, bukan $this
            return $query->whereMonth('inspection_date', $currentMonth)
                ->whereYear('inspection_date', $date->year);
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
    public function scopeSearchByType($query, $type)
    {
        if ($type) {
            return $query->whereHas('equipmentMaster', function ($q) use ($type) {
                $q->where('type', $type);
            });
        }
        return $query;
    }
}
