<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'menu',
        'request_route',
        'status',
        'route',
        'urutan',
        'icon',
    ];
    public function SubMenu()
    {
        return $this->hasMany(SubMenu::class)->where('status', 'enabled')->urutan();
    }
    public function scopeStatus($query)
    {
        $query->when(
            fn($query) => $query->where('status', 'enabled')
        );
    }
    public function scopeUrutan($query)
    {
        $query->when(
            fn($query) => $query->orderBy('urutan', 'ASC')
        );
    }
}
