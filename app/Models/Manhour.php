<?php

namespace App\Models;

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

    public function scopeDateRange(Builder $query, $startDate = null, $endDate = null): Builder
    {
        // Jika startDate ada, tambahkan kondisi >=
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$this->start_date, $this->end_date]);
        } elseif ($startDate) {
            $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('date', '<=', $endDate);
        } else {
            // Jika tidak ada filter tanggal, ambil data dari awal tahun berjalan
            $query->whereYear('date', now()->year);
        };

        return $query;
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
