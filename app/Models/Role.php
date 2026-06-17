<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name'];

    public function render()
    {
        // Menggunakan whereHas untuk mencari User yang memiliki role tertentu
        // Sesuaikan 'User' dengan nama role yang merepresentasikan Karyawan di tabel roles Anda.
        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'User');
        })->get();

        return view('livewire.mcu.generate-schedule', [
            'employees' => $employees
        ]);
    }
}
