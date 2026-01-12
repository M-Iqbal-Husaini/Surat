<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Surat extends Model
{
    protected $table = 'surat';

    protected $fillable = [
        'template_id',
        'jenis_surat_id',
        'alur_id',
        'pembuat_id',
        'unit_asal_id',
        'unit_tujuan_id',
        'perihal',
        'data_json',
        'status',
        'step_aktif',
        'signed_at',
        'signed_by_user_id',
        'signed_token',
        'tanggal_surat',
        'nomor_surat',
        'qr_svg',
        'is_completed',
    ];

    protected $casts = [
        'data_json'     => 'array',
        'signed_at'     => 'datetime',
        'tanggal_surat' => 'date',
        
    ];

    /* ================= RELATION ================= */

    public function template()
    {
        return $this->belongsTo(TemplateSurat::class, 'template_id');
    }

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class, 'jenis_surat_id');
    }

    public function alur()
    {
        return $this->belongsTo(AlurVerifikasi::class, 'alur_id');
    }

    public function steps()
    {
        return $this->hasMany(SuratVerifikasi::class, 'surat_id')
            ->orderBy('urutan');
    }

    /* ================= WORKFLOW ================= */

    public function currentStep()
    {
        return $this->steps()
            ->where('urutan', $this->step_aktif)
            ->first();
    }

    public function unitAsal()
    {
        return $this->belongsTo(Unit::class, 'unit_asal_id');
    }

    public function unitTujuan()
    {
        return $this->belongsTo(Unit::class, 'unit_tujuan_id');
    }

    public function signedUser()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by_user_id');
    }

    public function signedJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'signed_by_jabatan_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function verifikasi()
    {
        return $this->hasMany(SuratVerifikasi::class)
            ->orderBy('urutan');
    }

    public function syncVerifikasiStatus(string $status): void
    {
        $step = $this->verifikasi()
            ->where('urutan', $this->step_aktif)
            ->first();

        if (!$step) {
            return;
        }

        $map = [
            'diterima' => 'disetujui',
            'final'    => 'selesai',
            'ditolak'  => 'ditolak',
            'direvisi' => 'direvisi',
            'disposisi'=> 'disposisi',
        ];

        if (isset($map[$status])) {
            $step->update([
                'status'   => $map[$status],
                'acted_at'=> now(),
                'user_id' => auth()->id(),
            ]);
        }
    }

    // App\Models\Surat.php
    public function penandatangan()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    // App\Models\Surat.php
    public function disposisi()
    {
        return $this->hasMany(SuratDisposisi::class);
    }

    public function isAntarUnit(): bool
    {
        return $this->unit_asal_id !== $this->unit_tujuan_id;
    }

    public function stepAktif()
    {
        return $this->hasOne(SuratVerifikasi::class, 'surat_id')
            ->where('urutan', $this->step_aktif);
    }

    public function advanceStep(): void
    {
        DB::transaction(function () {
            // step sekarang
            $current = $this->verifikasi()
                ->where('urutan', $this->step_aktif)
                ->lockForUpdate()
                ->first();

            if (!$current) {
                return;
            }

            // tandai selesai
            $current->update([
                'status'   => 'selesai',
                'acted_at' => now(),
            ]);

            // step berikutnya
            $nextStep = $this->verifikasi()
                ->where('urutan', $this->step_aktif + 1)
                ->first();

            if ($nextStep) {
                $this->update([
                    'step_aktif' => $nextStep->urutan,
                ]);

                $nextStep->update([
                    'status' => 'pending',
                ]);
            } else {
                // tidak ada step lagi → final
                $this->update([
                    'status'       => 'final',
                    'is_completed' => true,
                ]);
            }
        });
    }

    private function normalizeNextStep(Surat $surat)
    {
        $nextStep = SuratVerifikasi::where('surat_id', $surat->id)
            ->where('urutan', $surat->step_aktif)
            ->first();

        if ($nextStep && $nextStep->status === 'draft') {
            $nextStep->update([
                'status' => 'pending',
            ]);
        }
    }

    public function disposisiAktif()
    {
        return $this->hasOne(SuratDisposisi::class)
            ->where('status', 'pending')
            ->latest();
    }

    public function canShowQr(): bool
    {
        return in_array($this->status, ['diajukan', 'final'])
            && !empty($this->qr_svg);
    }
    


}
