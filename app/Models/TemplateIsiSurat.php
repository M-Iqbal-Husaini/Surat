<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateIsiSurat extends Model
{
    protected $table = 'template_isi_surat';

    protected $fillable = [
        'template_id',
        'field_key',
        'label',
        'type',
        'required',
        'order',
    ];

    public function template()
    {
        return $this->belongsTo(TemplateSurat::class, 'template_id');
    }

    public function penandatanganJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'penandatangan_jabatan_id');
    }

}
