<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'unit_id',
        'jabatan_id',
        'status',
        'ttd_path',              // ✅ PENTING
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* =================
     | RELATION
     ================= */

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function suratDibuat()
    {
        return $this->hasMany(Surat::class, 'pembuat_id');
    }

    /* =================
     | SCOPE
     ================= */

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /* =================
     | HELPER
     ================= */

    public function hasTtd(): bool
    {
        return !empty($this->ttd_path);
    }

    public function getTtdUrlAttribute(): ?string
    {
        return $this->ttd_path
            ? asset('storage/' . $this->ttd_path)
            : null;
    }
}
