<?php

namespace App\Models;

use App\Models\Scopes\EnabledEventTypeScope;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $table = 'event_types';

    protected $fillable = [
        'event_category_id',
        'event_type_name',
        'status'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new EnabledEventTypeScope);
    }
    public function scopeSearch($query, $term)
    {
        return $query->where('event_type_name',  'like', '%' . $term . '%');
    }
    public function scopeSearchEventCategory($query, $term)
    {
        return $query->where('event_category_id',  'like', '%' . $term . '%');
    }
    public function EventCategories()
    {
        return $this->belongsTo(EventCategory::class,'event_category_id');
    }
    public function EventSubType()
    {
        return $this->hasMany(EventSubType::class);
    }

}
