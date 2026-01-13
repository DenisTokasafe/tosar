<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpiFinding extends Model
{
    protected $fillable = [
    'wpi_report_id',
    'ohs_risk',
    'description',
    'prevention_action',
    'pic_responsible',
    'due_date',
    'completion_date',
    'photos',
    'photos_prevention'
];
    // App/Models/WpiFinding.php
    protected $casts = [
        'photos' => 'array',
        'photos_prevention' => 'array',
        'due_date' => 'date',
        'completion_date' => 'date',
        'pic_responsible' => 'array', // Otomatis mengubah Array ke JSON saat simpan, dan sebaliknya
    ];
}
