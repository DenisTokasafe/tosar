<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manhour extends Model
{
    protected $table = 'manhours';
    protected $fillable = [
        'date',
        'company_category',
        'company',
        'department',
        'dept_group',
        'job_class',
        'manhours',
        'manpower',
    ];
}
