<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionSession extends Model
{
    protected $fillable = [
        'inspection_date', // Tambahkan ini
        'inspection_number',
        'inspected_by',
        'area_photo_path',
        'documentation_path',
        'area_name',
    ];
    protected $casts = [
        'inspected_by' => 'array', // Karena Anda menggunakan pembatas '|'
        'inspection_date' => 'date',
    ];
}
