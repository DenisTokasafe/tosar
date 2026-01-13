<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpiReport extends Model
{
    protected $fillable = [
        'report_date',
        'report_time',
        'location',
        'department',
        'inspectors',
        'site_name',
        'area'
    ];
    protected $casts = [
        'inspectors' => 'array',
        'report_date' => 'date'
    ];

    public function findings()
    {
        return $this->hasMany(WpiFinding::class);
    }
}
