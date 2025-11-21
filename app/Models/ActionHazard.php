<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActionHazard extends Model
{
    use HasFactory;

    protected $casts = [
        'due_date' => 'datetime',
        'actual_close_date' => 'datetime',
    ];
    protected $fillable = [
        'hazard_id',
        'original_date',      // atau 'original_date'
        'description',
        'due_date',
        'actual_close_date',
        'responsible_id',
    ];


     // === BOOTED EVENT HOOKS ===
    protected static function booted()
    {
        // Saat CREATE
        static::created(function ($action) {
            activity()
                ->performedOn($action->hazard)        // subject = parent Hazard
                ->causedBy(auth()->user())            // user yang login (optional)
                ->withProperties([
                    'action_hazard_id' => $action->id,
                    'description'      => $action->description,
                    'due_date'         => $action->due_date,
                    'event'            => 'created',
                ])
                ->log('ActionHazard ditambahkan');
        });

        // Saat UPDATE
        static::updated(function ($action) {
            activity()
                ->performedOn($action->hazard)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action_hazard_id' => $action->id,
                    'changes'          => $action->getChanges(),
                    'event'            => 'updated',
                ])
                ->log('ActionHazard diperbarui');
        });

        // Saat DELETE
        static::deleted(function ($action) {
            activity()
                ->performedOn($action->hazard)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action_hazard_id' => $action->id,
                    'description'      => $action->description,
                    'event'            => 'deleted',
                ])
                ->log('ActionHazard dihapus');
        });
    }

    // Relasi
    public function hazard()
    {
        return $this->belongsTo(Hazard::class);
    }
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
