<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class IncidentImpact extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    /**
     * Konfigurasi Logging untuk Dampak Insiden
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('IncidentDetail')
            ->setDescriptionForEvent(fn(string $eventName) => "Incident Impact has been {$eventName}");
    }

    /**
     * Mencegat aktivitas untuk merekam label Body Part dan Injury Type
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $properties = $activity->properties->toArray();

        if (isset($properties['attributes'])) {
            foreach ($properties['attributes'] as $field => $value) {
                if ($field === 'body_part_id') {
                    // Rekam nama bagian tubuh untuk nilai baru
                    $properties['attributes']['body_part_label'] = \App\Models\BodyPart::find($value)?->name ?? 'Unknown Part';

                    // Rekam nama bagian tubuh untuk nilai lama jika ada
                    if (isset($properties['old']['body_part_id'])) {
                        $oldId = $properties['old']['body_part_id'];
                        $properties['old']['body_part_label'] = \App\Models\BodyPart::find($oldId)?->name ?? 'Unknown Part';
                    }
                }
            }

            // Tambahkan ringkasan dampak agar log mudah dibaca sekilas
            if (isset($properties['attributes']['injury_type'])) {
                $properties['attributes']['impact_summary'] = $properties['attributes']['injury_type'];
            }

            $activity->properties = collect($properties);
        }
    }

    /**
     * Relasi ke tabel body_parts
     */
    public function bodyPart(): BelongsTo
    {
        return $this->belongsTo(BodyPart::class, 'body_part_id');
    }

    /**
     * Relasi ke laporan induk
     */
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }
}
