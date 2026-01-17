<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Surat;
use App\Models\SuratDisposisi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        return match (true) {
            $user->hasRole('admin')               => $this->adminDashboard(),
            $user->hasRole('pimpinan')            => $this->pimpinanDashboard($user),
            $user->hasRole('verifikator')          => $this->verifikatorDashboard($user),
            $user->hasRole('sekretaris_direktur')  => $this->sekretarisDashboard($user),
            $user->hasRole('pembuat_surat')        => $this->pembuatSuratDashboard($user),
            default                                => abort(403),
        };
    }

    /* =========================================================
     * CORE ANALYTICS (BERBASIS UNIT / ROLE)
     * ========================================================= */
    protected function analyticsSurat(callable $queryMasuk, callable $queryKeluar)
    {
        $tahun = now()->year;

        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $masuk  = array_fill(0, 12, 0);
        $keluar = array_fill(0, 12, 0);

        $queryMasuk(
            Surat::whereYear('tanggal_surat', $tahun)
        )->get(['tanggal_surat'])->each(function ($s) use (&$masuk) {
            $index = Carbon::parse($s->tanggal_surat)->month - 1;
            $masuk[$index]++;
        });

        $queryKeluar(
            Surat::whereYear('tanggal_surat', $tahun)
        )->get(['tanggal_surat'])->each(function ($s) use (&$keluar) {
            $index = Carbon::parse($s->tanggal_surat)->month - 1;
            $keluar[$index]++;
        });

        return [
            'chartLabels' => $labels,
            'chartData'   => [
                [
                    'label' => 'Surat Masuk',
                    'data'  => $masuk,
                ],
                [
                    'label' => 'Surat Keluar',
                    'data'  => $keluar,
                ],
            ],
            'tahun' => $tahun,
        ];
    }


    /* =========================================================
     * ADMIN (GLOBAL)
     * ========================================================= */
    protected function adminDashboard()
    {
        return view('admin.dashboard', array_merge([
            'jumlahUser'        => User::count(),
            'jumlahSuratMasuk'  => Surat::whereNotNull('unit_tujuan_id')->count(),
            'jumlahSuratKeluar' => Surat::whereNotNull('pembuat_id')->count(),
        ], $this->analyticsSurat(
            fn ($q) => $q->whereNotNull('unit_tujuan_id'),
            fn ($q) => $q->whereNotNull('pembuat_id')
        )));
    }

    /* =========================================================
     * PIMPINAN (BERBASIS UNIT TUJUAN)
     * ========================================================= */
    protected function pimpinanDashboard(User $user)
    {
        abort_if(!$user->unit_id || !$user->jabatan_id, 500);

        $jumlahSuratMasuk = Surat::where('unit_tujuan_id', $user->unit_id)->count();

        $jumlahSuratKeluar = Surat::where('unit_tujuan_id', $user->unit_id)
            ->where('status', '!=', 'ditolak')
            ->whereHas('verifikasi', fn ($q) =>
                $q->where('jabatan_id', $user->jabatan_id)
            )
            ->whereDoesntHave('verifikasi', fn ($v) =>
                $v->where('jabatan_id', $user->jabatan_id)
                ->whereColumn('urutan', 'surat.step_aktif')
                ->where('status', 'pending')
            )
            ->count();

        return view('pimpinan.dashboard', array_merge([
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => $jumlahSuratKeluar,
        ], $this->analyticsSurat(
            fn ($q) => $q->where('unit_tujuan_id', $user->unit_id),
            fn ($q) => $q
                ->where('unit_tujuan_id', $user->unit_id)
                ->where('status', '!=', 'ditolak')
        )));
    }


    /* =========================================================
     * VERIFIKATOR (UNIT + JABATAN)
     * ========================================================= */
    protected function verifikatorDashboard(User $user)
    {
        abort_if(!$user->unit_id || !$user->jabatan_id, 500);

        $jumlahSuratMasuk = Surat::where('unit_tujuan_id', $user->unit_id)
            ->whereHas('verifikasi', fn ($q) =>
                $q->where('jabatan_id', $user->jabatan_id)
            )
            ->count();

        $jumlahSuratKeluar = Surat::where('unit_tujuan_id', $user->unit_id)
            ->where('status', '!=', 'ditolak')
            ->whereHas('verifikasi', fn ($q) =>
                $q->where('jabatan_id', $user->jabatan_id)
            )
            ->whereDoesntHave('verifikasi', fn ($v) =>
                $v->where('jabatan_id', $user->jabatan_id)
                ->whereColumn('urutan', 'surat.step_aktif')
                ->where('status', 'pending')
            )
            ->count();

        return view('verifikator.dashboard', array_merge([
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => $jumlahSuratKeluar,
        ], $this->analyticsSurat(
            fn ($q) => $q
                ->where('unit_tujuan_id', $user->unit_id)
                ->whereHas('verifikasi', fn ($v) =>
                    $v->where('jabatan_id', $user->jabatan_id)
                ),
            fn ($q) => $q
                ->where('unit_tujuan_id', $user->unit_id)
                ->where('status', '!=', 'ditolak')
                ->whereHas('verifikasi', fn ($v) =>
                    $v->where('jabatan_id', $user->jabatan_id)
                )
        )));
    }


    /* =========================================================
     * SEKRETARIS DIREKTUR
     * ========================================================= */
    protected function sekretarisDashboard(User $user)
    {
        abort_if(!$user->unit_id || !$user->jabatan_id, 500);

        $jumlahSuratMasuk = Surat::where('unit_tujuan_id', $user->unit_id)->count();

        $jumlahSuratKeluar = Surat::where('unit_tujuan_id', $user->unit_id)
            ->where('status', '!=', 'ditolak')
            ->whereHas('verifikasi', fn ($q) =>
                $q->where('jabatan_id', $user->jabatan_id)
            )
            ->whereDoesntHave('verifikasi', fn ($v) =>
                $v->where('jabatan_id', $user->jabatan_id)
                ->whereColumn('urutan', 'surat.step_aktif')
                ->where('status', 'pending')
            )
            ->count();

        return view('sekretaris-direktur.dashboard', array_merge([
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => $jumlahSuratKeluar,
        ], $this->analyticsSurat(
            fn ($q) => $q->where('unit_tujuan_id', $user->unit_id),
            fn ($q) => $q
                ->where('unit_tujuan_id', $user->unit_id)
                ->where('status', '!=', 'ditolak')
        )));
    }


    /* =========================================================
     * PEMBUAT SURAT (UNIT TUJUAN = SURAT MASUK)
     * ========================================================= */
    protected function pembuatSuratDashboard(User $user)
    {
        abort_if(!$user->unit_id || !$user->jabatan_id, 500);

        /* =========================================================
        * SURAT MASUK (BERDASARKAN UNIT TUJUAN)
        * ========================================================= */
        $jumlahSuratMasuk = Surat::where('unit_tujuan_id', $user->unit_id)
            ->where('pembuat_id', '!=', $user->id)
            ->count();

        /* =========================================================
        * SURAT KELUAR (SURAT YANG SAYA BUAT)
        * ========================================================= */
        $jumlahSuratKeluar = Surat::where('pembuat_id', $user->id)->count();

        /* =========================================================
        * DISPOSISI AKTIF KE JABATAN SAYA
        * ========================================================= */
        $jumlahDisposisi = SuratDisposisi::where('ke_jabatan_id', $user->jabatan_id)
            ->where('status', 'pending')
            ->count();

        /* =========================================================
        * ANALYTICS (CHART)
        * ========================================================= */
        $analytics = $this->analyticsSurat(
            // Surat Masuk (unit tujuan)
            fn ($q) => $q
                ->where('unit_tujuan_id', $user->unit_id)
                ->where('pembuat_id', '!=', $user->id),

            // Surat Keluar (dibuat oleh saya)
            fn ($q) => $q->where('pembuat_id', $user->id)
        );

        return view('pembuat-surat.dashboard', array_merge([
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => $jumlahSuratKeluar,
            'jumlahDisposisi'   => $jumlahDisposisi,
        ], $analytics));
    }

}
