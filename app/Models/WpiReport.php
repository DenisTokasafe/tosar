<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpiReport extends Model
{
    protected $casts = [
    'inspectors' => 'array',
    'report_date' => 'date'
];

    public function findings()
    {
        return $this->hasMany(WpiFinding::class);
    }
}
