<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'nama_unit',
        'kode_unit',
        'jenis_unit',
        'parent_id',
    ];

    /* =================
     | RELATION
     ================= */

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function jabatans()
    {
        return $this->hasMany(Jabatan::class);
    }

    /* =================
     | SCOPE
     ================= */

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeJurusan($query)
    {
        return $query->where('jenis_unit', 'jurusan');
    }

    public function scopeUnitPendukung($query)
    {
        return $query->where('jenis_unit', 'unit');
    }

    public function pimpinan()
    {
        return $this->belongsTo(User::class, 'pimpinan_user_id');
    }
}
