<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlurVerifikasiStep extends Model
{
    protected $table = 'alur_verifikasi_steps';

    protected $fillable = [
        'alur_verifikasi_id',
        'urutan',
        'unit_scope',
        'fase', // 🔥 WAJIB
        'jabatan_id',   // 🔥
        'perlu_ttd',
        'boleh_kembalikan',
    ];

    public function alur()
    {
        return $this->belongsTo(AlurVerifikasi::class);
    }

        // 🔥 INI YANG KURANG
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->fase === null) {
                throw new \LogicException('Fase wajib diisi');
            }
        });
    }

}
