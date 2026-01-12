<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratVerifikasi extends Model
{
    protected $table = 'surat_verifikasi';

    protected $fillable = [
        'surat_id',
        'unit_id',
        'jabatan_id',   // 🔥 WAJIB
        'urutan',
        'status',
        'catatan',
        'perlu_ttd',
        'user_id',
        'acted_at',
        
    ];

    protected $casts = [
        'perlu_ttd' => 'boolean',
        'acted_at'  => 'datetime',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }



}
