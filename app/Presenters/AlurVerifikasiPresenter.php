<?php

namespace App\Presenters;

use App\Models\Surat;

class AlurVerifikasiPresenter
{
    public static function map(Surat $surat)
    {
        return $surat->verifikasi
            ->sortBy('urutan')
            ->map(function ($step) use ($surat) {

                // 🔴 GLOBAL TERMINAL STATE
                if ($surat->status === 'ditolak') {
                    return [
                        'urutan'     => $step->urutan,
                        'jabatan'    => $step->jabatan?->nama_jabatan ?? '-',
                        'perlu_ttd'  => (bool) $step->perlu_ttd,
                        'state'      => 'ditolak',
                        'raw_status' => $step->status,
                    ];
                }

                // =========================
                // PRIORITAS STATUS STEP
                // =========================
                if (
                    $step->status === 'disposisi'
                    && $surat->status === 'disposisi'
                ) {
                    $state = 'disposisi';
                }
                elseif ($step->status === 'pending' && $step->urutan === $surat->step_aktif) {
                    $state = 'active';
                }
                elseif (in_array($step->status, ['selesai', 'ditandatangani'])) {
                    $state = 'done';
                }
                elseif ($step->status === 'direvisi') {
                    $state = 'direvisi';
                }
                else {
                    $state = 'waiting';
                }


                return [
                    'urutan'     => $step->urutan,
                    'jabatan'    => $step->jabatan?->nama_jabatan ?? '-',
                    'perlu_ttd'  => (bool) $step->perlu_ttd,
                    'state'      => $state,
                    'raw_status' => $step->status,
                ];
            })
            ->values();
    }

}
