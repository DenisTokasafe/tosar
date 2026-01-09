<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpiFinding extends Model
{
    // App/Models/WpiFinding.php
    protected $casts = [
        'photos' => 'array',
        'due_date' => 'date'
    ];
}
