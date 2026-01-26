<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FireProtection extends Model
{
    protected $fillable = [
        'type',
        'location',
        'equipment_no',
        'inspection_date',
        'inspected_by',
        'conditions',
        'remarks',
        'documentation_path',
    ];
    protected $casts = [
    'conditions' => 'array',
    'inspected_by' => 'string', // Karena Anda menggunakan pembatas '|'
    'inspection_date' => 'date',
];
}
