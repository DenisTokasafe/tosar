<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HazardWorkflow extends Model
{
    protected $table='hazard_workflows';
    protected $fillable = ['from_status', 'to_status', 'role','from_inisial','to_inisial'];

    public static function isValidTransition($from, $to, $role): bool
    {
        return self::where('from_status', $from)
            ->where('to_status', $to)
            ->where('role', $role)
            ->exists();
    }

    public static function getAvailableTransitions(string $fromStatus, string $role): array
    {
        return self::where('from_status', $fromStatus)
            ->where('role', $role)
            ->pluck('to_status','to_inisial')
            ->unique()
            ->toArray();
    }
}

