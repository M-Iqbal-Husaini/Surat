<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    protected $table = 'template_surat';

    protected $fillable = [
        'kode_template',
        'nama_template',
        'jenis_surat_id',
        'kode_alur',
        'penandatangan_jabatan_id',
        'body_html',
        'is_active',
    ];

    /* ================= RELATION ================= */

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function alurVerifikasi()
    {
        return $this->belongsTo(
            AlurVerifikasi::class,
            'kode_alur',
            'kode_alur'
        );
    }

    public function fields()
    {
        return $this->hasMany(TemplateIsiSurat::class, 'template_id')
            ->orderBy('order');
    }

    public function penandatangan()
    {
        return $this->belongsTo(Jabatan::class, 'penandatangan_jabatan_id');
    }
}
