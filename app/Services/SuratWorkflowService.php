<?php

namespace App\Services;

use App\Models\Surat;
use App\Models\SuratVerifikasi;
use App\Models\SuratDisposisi;
use Illuminate\Support\Facades\DB;
use App\Services\FinalisasiSuratService;
use LogicException;

class SuratWorkflowService
{
    private static function guardFinal(Surat $surat): void
    {
        if ($surat->status === 'final') {
            throw new LogicException('Surat sudah final.');
        }
    }

    private static function perluTtdPembuat(Surat $surat): bool
    {
        // internal unit
        if ((int) $surat->unit_asal_id === (int) $surat->unit_tujuan_id) {
            return false;
        }

        // antar unit ke DIREKTORAT → TIDAK perlu TTD pembuat
        if ($surat->unitTujuan?->jenis_unit === 'direktorat') {
            return false;
        }

        // selain itu → WAJIB TTD pembuat
        return true;
    }


    private static function isStepPembuat(SuratVerifikasi $step): bool
    {
        return (int) $step->urutan === 1;
    }


    private static function stepAktif(Surat $surat): SuratVerifikasi
    {
        return SuratVerifikasi::where('surat_id', $surat->id)
            ->where('urutan', $surat->step_aktif)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private static function nextStep(SuratVerifikasi $step): ?SuratVerifikasi
    {
        return SuratVerifikasi::where('surat_id', $step->surat_id)
            ->where('urutan', '>', $step->urutan)
            ->orderBy('urutan')
            ->first();
    }

    private static function isLastStep(SuratVerifikasi $step): bool
    {
        return !self::nextStep($step);
    }

    public static function submitByPembuat(Surat $surat, $user): void
    {
        self::guardFinal($surat);
        abort_if($surat->pembuat_id !== $user->id, 403);

        DB::transaction(function () use ($surat, $user) {
            $step = self::stepAktif($surat);

            abort_if(!self::isStepPembuat($step), 422);

            $perluTtdPembuat = self::perluTtdPembuat($surat);

            if ($perluTtdPembuat && !$step->perlu_ttd) {
                throw new LogicException(
                    'Surat ini wajib ditandatangani pembuat.'
                );
            }

            if (!$perluTtdPembuat && $step->perlu_ttd) {
                throw new LogicException(
                    'Surat ini tidak memerlukan tanda tangan pembuat.'
                );
            }

            /**
             * HANDLE REVISI
             */
            if ($step->status === 'direvisi') {
                $step->update([
                    'status'   => 'pending',
                    'catatan'  => null,
                    'acted_at' => null,
                    'user_id'  => null,
                ]);

                $surat->update(['status' => 'diajukan']);
                return;
            }

            abort_if(!in_array($step->status, ['draft', 'pending']), 422);

            /**
             * EKSEKUSI STEP PEMBUAT
             */
            if ($step->perlu_ttd) {

                // ✅ TTD DIGITAL PEMBUAT
                FinalisasiSuratService::ttdPembuat($surat, $user);

                $step->update([
                    'status'   => 'ditandatangani',
                    'user_id'  => $user->id,
                    'acted_at' => now(),
                ]);

            } else {
                $step->update([
                    'status'   => 'selesai',
                    'user_id'  => $user->id,
                    'acted_at' => now(),
                ]);
            }

            /**
             * LANJUT STEP
             */
            $next = self::nextStep($step);

            if ($next) {
                $surat->update([
                    'status'     => 'diajukan',
                    'step_aktif' => $next->urutan,
                ]);

                $next->update([
                    'status'   => 'pending',
                    'acted_at' => now(),
                ]);
                return;
            }

            FinalisasiSuratService::finalisasi($surat, $user);
        });
    }


    public static function approve(Surat $surat, $user): void
    {
        self::guardFinal($surat);

        DB::transaction(function () use ($surat, $user) {

            $step = self::stepAktif($surat);

            abort_if(
                $step->jabatan_id !== $user->jabatan_id ||
                $step->status !== 'pending',
                403
            );

            // 1️⃣ Tandai verifikasi SELESAI
            $step->update([
                'status'   => 'selesai',
                'user_id'  => $user->id,
                'acted_at' => now(),
            ]);

            // 2️⃣ Jika perlu TTD, JANGAN lanjut step
            if ($step->perlu_ttd) {
                $surat->update([
                    'status' => 'menunggu_ttd',
                ]);
                return;
            }

            // 3️⃣ Lanjut step berikutnya
            $next = self::nextStep($step);

            if ($next) {
                $surat->update([
                    'status'     => 'diproses',
                    'step_aktif' => $next->urutan,
                ]);

                $next->update([
                    'status'   => 'pending',
                    'acted_at' => now(),
                ]);
                return;
            }

            FinalisasiSuratService::finalisasi($surat, $user);
        });
    }

    public static function revise(Surat $surat, $user, string $catatan): void
    {
        self::guardFinal($surat);

        DB::transaction(function () use ($surat, $user, $catatan) {
            $step = self::stepAktif($surat);

            abort_if(
                $step->jabatan_id !== $user->jabatan_id ||
                $step->status !== 'pending',
                403
            );

            $stepPembuat = SuratVerifikasi::where('surat_id', $surat->id)
                ->where('urutan', 1)
                ->firstOrFail();

            $step->update([
                'status'   => 'menunggu_revisi',
                'catatan'  => $catatan,
                'user_id'  => $user->id,
                'acted_at' => now(),
            ]);

            $stepPembuat->update([
                'status'   => 'direvisi',
                'catatan'  => $catatan,
                'acted_at' => now(),
            ]);

            $surat->update([
                'status'     => 'direvisi',
                'step_aktif' => 1,
            ]);
        });
    }

    public static function reject(Surat $surat, $user, ?string $catatan = null): void
    {
        self::guardFinal($surat);

        DB::transaction(function () use ($surat, $user, $catatan) {
            $step = self::stepAktif($surat);

            abort_if(
                $step->jabatan_id !== $user->jabatan_id ||
                $step->status !== 'pending',
                403
            );

            $step->update([
                'status'   => 'ditolak',
                'catatan'  => $catatan,
                'user_id'  => $user->id,
                'acted_at' => now(),
            ]);

            $surat->update([
                'status' => 'ditolak',
            ]);
        });
    }

    public static function disposisi(
        Surat $surat,
        $user,
        int $keJabatanId,
        string $instruksi
    ): void {
        self::guardFinal($surat);

        DB::transaction(function () use ($surat, $user, $keJabatanId, $instruksi) {

            // 1️⃣ Pastikan user pernah terlibat di alur surat
            $stepSaya = SuratVerifikasi::where('surat_id', $surat->id)
                ->where('jabatan_id', $user->jabatan_id)
                ->first();

            abort_if(!$stepSaya, 403);

            // 2️⃣ Tandai disposisi aktif (TANPA SYARAT step_aktif)
            SuratDisposisi::create([
                'surat_id'        => $surat->id,
                'dari_jabatan_id' => $user->jabatan_id,
                'ke_jabatan_id'   => $keJabatanId,
                'instruksi'       => $instruksi,
                'status'          => 'pending',
            ]);

            // 3️⃣ Update status surat
            $surat->update([
                'status' => 'disposisi',
            ]);

            // 4️⃣ Tandai step yang sedang berjalan (jika ada)
            SuratVerifikasi::where('surat_id', $surat->id)
                ->where('status', 'pending')
                ->update([
                    'status'   => 'disposisi',
                    'acted_at'=> now(),
                ]);
        });
    }

    public static function tindaklanjutiDisposisi(Surat $surat, $user): void
    {
        self::guardFinal($surat);

        DB::transaction(function () use ($surat, $user) {

            $disposisi = $surat->disposisiAktif;

            abort_if(
                !$disposisi ||
                $disposisi->ke_jabatan_id !== $user->jabatan_id ||
                $disposisi->status !== 'pending',
                403
            );

            // 1️⃣ Selesaikan disposisi
            $disposisi->update([
                'status'          => 'selesai',
                'tindaklanjut_at' => now(),
            ]);

            // 2️⃣ Ambil step yang tadi DISPOSISI
            $step = SuratVerifikasi::where('surat_id', $surat->id)
                ->where('status', 'disposisi')
                ->lockForUpdate()
                ->firstOrFail();

            // 3️⃣ Tandai step SELESAI (BUKAN pending)
            $step->update([
                'status'   => 'selesai',
                'acted_at' => now(),
            ]);

            // 4️⃣ Lanjut ke step berikutnya
            $next = self::nextStep($step);

            if ($next) {
                $surat->update([
                    'status'     => 'diproses',
                    'step_aktif' => $next->urutan,
                ]);

                $next->update([
                    'status'   => 'pending',
                    'acted_at' => now(),
                ]);

                return;
            }

            // 5️⃣ Jika tidak ada step lagi → FINAL
            FinalisasiSuratService::finalisasi($surat, $user);
        });
    }


    public static function sign(Surat $surat, $user): void
    {
        self::guardFinal($surat);

        DB::transaction(function () use ($surat, $user) {

            // 🔒 Ambil step aktif
            $step = self::stepAktif($surat);

            // ❌ Validasi keras
            abort_if(
                !$step->perlu_ttd ||
                $step->jabatan_id !== $user->jabatan_id ||
                $step->status !== 'selesai', // ⬅️ INI KUNCI UTAMA
                403
            );

            // ✍️ TTD oleh pejabat
            $step->update([
                'status'   => 'ditandatangani',
                'user_id'  => $user->id,
                'acted_at' => now(),
            ]);

            // 🔚 Jika step terakhir → finalisasi
            if (self::isLastStep($step)) {
                FinalisasiSuratService::finalisasi($surat, $user);
                return;
            }

            // ➡️ Lanjut ke step berikutnya
            $next = self::nextStep($step);

            $surat->update([
                'status'     => 'diproses',
                'step_aktif' => $next->urutan,
            ]);

            $next->update([
                'status'   => 'pending',
                'acted_at' => now(),
            ]);
        });
    }

}
