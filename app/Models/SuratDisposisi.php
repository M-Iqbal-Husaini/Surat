<?php

namespace App\Models;

use App\Models\Jabatan;
use Illuminate\Database\Eloquent\Model;

class SuratDisposisi extends Model
{
    protected $table = 'surat_disposisi';

    protected $fillable = [
        'surat_id',
        'dari_jabatan_id',
        'ke_jabatan_id',
        'instruksi',
        'status',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function dariJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'dari_jabatan_id');
    }

    public function keJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'ke_jabatan_id');
    }
}
