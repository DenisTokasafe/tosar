<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class WpiReport extends Model
{
    use LogsActivity;
    protected $fillable = [
        'report_date',
        'report_time',
        'location',
        'department',
        'inspectors',
        'site_name',
        'area',
        'status',
        'created_by',
        'department_id',
        'contractor_id'
    ];
    protected $casts = [
        'inspectors' => 'array',
        'report_date' => 'date'
    ];
    /**
     * Activity Log Options
     */
    public function getActivitylogOptions(): LogOptions
    {
        // Catat semua field, tapi log *_id diganti nama lewat accessor
        return LogOptions::defaults()
            ->useLogName($this->getTable())
            ->logAll()
            ->logOnlyDirty();
    }

    /**
     * Tap activity untuk menambahkan nama relasi
     */
    /**
     * Memanipulasi properti activity log sebelum disimpan agar ID
     * berubah menjadi Nama yang dapat dibaca.
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        // Mapping ID ke Nama berdasarkan relasi yang ada di model WpiReport
        $map = [
            'created_by'    => fn($id) => $id ? \App\Models\User::find($id)?->name : 'System',
            'department_id' => fn($id) => $id ? $this->department_rel?->department_name : null,
            'contractor_id' => fn($id) => $id ? $this->contractor_rel?->contractor_name : null,
            // Tambahkan field lain jika Anda menambahkannya di migration nanti
        ];

        $properties = $activity->properties->toArray();

        foreach (['attributes', 'old'] as $key) {
            if (!isset($properties[$key])) {
                continue;
            }

            foreach ($map as $field => $resolver) {
                if (isset($properties[$key][$field])) {
                    // Kita tambahkan suffix '_name' agar data ID asli tetap ada namun kita punya labelnya
                    $properties[$key][$field . '_label'] = $resolver($properties[$key][$field]);
                }
            }
        }

        $activity->properties = $properties;
    }

    /** RELATIONS */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function findings()
    {
        return $this->hasMany(WpiFinding::class);
    }
    /**
     * Relasi ke Department untuk filter Moderator/ERM
     */
    public function department_rel()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Relasi ke Contractor untuk filter Moderator/ERM
     */
    public function contractor_rel()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    /**
     * Helper untuk cek jika laporan sudah selesai
     */
    public function isClosed(): bool
    {
        return in_array(strtolower($this->status), ['closed', 'cancelled']);
    }
}
