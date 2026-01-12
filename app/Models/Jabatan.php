<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = [
        'nama_jabatan',
        'unit_id',
        'level',
        'is_pimpinan',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopePimpinan($query)
    {
        return $query->where('is_pimpinan', true);
    }
}
