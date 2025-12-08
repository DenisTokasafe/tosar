<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

       public function scopeDateRange(Builder $query, string $startDate, string $endDate): void
    {
        // Jika tidak ada tanggal yang dipilih, jangan terapkan filter
        if (is_null($startDate) && is_null($endDate)) {
            return;
        }

        // Filter jika hanya tanggal awal yang ada
        if (!is_null($startDate) && is_null($endDate)) {
            $startDateFormatted = Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay();
            $query->where('date', '===', $startDateFormatted);
            return;
        }

        // Filter jika hanya tanggal akhir yang ada
        if (is_null($startDate) && !is_null($endDate)) {
            $endDateFormatted = Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay();
            $query->where('date', '<=', $endDateFormatted);
            return;
        }

        // Filter jika kedua tanggal ada (rentang penuh)
        $startDateFormatted = Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay();
        $endDateFormatted = Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay();

        $query->whereBetween('date', [$startDateFormatted, $endDateFormatted]);
    }
     public function scopeSearch(Builder $query, $search = null): Builder
     {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('department', 'like', '%' . $search . '%')
                  ->orWhere('company', 'like', '%' . $search . '%')
                  ->orWhere('company_category', 'like', '%' . $search . '%');
            });
        }
        return $query;
     }
}
