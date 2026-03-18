<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestigationTeam extends Model
{
    protected $guarded = ['id'];

    // Relasi balik ke laporan utama
    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }

    // Relasi ke tabel User untuk mengambil Nama Foto Profil, dll.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
