<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlurVerifikasi extends Model
{
    protected $table = 'alur_verifikasi';

    protected $fillable = [
        'kode_alur',
        'nama_alur',
        'deskripsi',
        'is_active',
    ];

    public function steps()
    {
        return $this->hasMany(AlurVerifikasiStep::class, 'alur_verifikasi_id')
            ->orderBy('urutan');
    }
}
