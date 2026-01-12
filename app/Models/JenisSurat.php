<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    protected $table = 'jenis_surat';

    protected $fillable = [
        'kode',
        'nama',
        'boleh_ttd_pembuat',
    ];

    public function templates()
    {
        return $this->hasMany(TemplateSurat::class);
    }
}
