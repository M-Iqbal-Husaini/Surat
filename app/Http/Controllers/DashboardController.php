<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Disposisi;
use App\Models\TemplateSurat;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // admin IT = super admin sistem
        if ($user->hasRole('admin')) {
            return $this->adminDashboard($user);
        }

        // pimpinan (jurusan / institusi)
        if ($user->hasAnyRole(['pimpinan'])) {
            return $this->pimpinanDashboard($user);
        }

        // verifikator
        if ($user->hasRole('verifikator')) {
            return $this->verifikatorDashboard($user);
        }

        // sekretaris unit
        if ($user->hasRole('sekretaris_unit')) {
            return $this->sekretarisDashboard($user);
        }

        // pembuat surat
        if ($user->hasRole('pembuat_surat')) {
            return $this->pembuatSuratDashboard($user);
        }

        // fallback kalau belum punya role / untuk testing
        abort(403, 'Anda tidak memiliki akses ke dashboard.');
    }

    protected function adminDashboard(User $user)
    {
        $data = [
            'jumlahUser'          => User::count(),
            'jumlahSuratMasuk'    => class_exists(SuratMasuk::class)   ? SuratMasuk::count()   : 0,
            'jumlahSuratKeluar'   => class_exists(SuratKeluar::class)  ? SuratKeluar::count()  : 0,
            'jumlahDisposisi'     => class_exists(Disposisi::class)    ? Disposisi::count()    : 0,
            'jumlahTemplateSurat' => class_exists(TemplateSurat::class)? TemplateSurat::count(): 0,
        ];

        return view('admin.dashboard', $data);
    }

    protected function pimpinanDashboard(User $user)
    {
        $data = [
            'jumlahSuratMasuk'    => class_exists(SuratMasuk::class)   ? SuratMasuk::count()   : 0,
            'jumlahSuratKeluar'   => class_exists(SuratKeluar::class)  ? SuratKeluar::count()  : 0,
            'jumlahDisposisi'     => class_exists(Disposisi::class)    ? Disposisi::count()    : 0,
        ];

        return view('pimpinan.dashboard', $data);
    }

    protected function verifikatorDashboard(User $user)
    {
        $data = [
            'jumlahSuratMasuk'    => class_exists(SuratMasuk::class)   ? SuratMasuk::count()   : 0,
            'jumlahSuratKeluar'   => class_exists(SuratKeluar::class)  ? SuratKeluar::count()  : 0,
        ];

        return view('verifikator.dashboard', $data);
    }

    protected function sekretarisDashboard(User $user)
    {
        $data = [
            'jumlahSuratMasuk'    => class_exists(SuratMasuk::class)   ? SuratMasuk::count()   : 0,
            'jumlahSuratKeluar'   => class_exists(SuratKeluar::class)  ? SuratKeluar::count()  : 0,
            'jumlahDisposisi'     => class_exists(Disposisi::class)    ? Disposisi::count()    : 0,
        ];

        return view('sekretaris-unit.dashboard', $data);
    }

    protected function pembuatSuratDashboard(User $user)
    {
        $data = [
            'jumlahSuratMasuk'    => class_exists(SuratMasuk::class)   ? SuratMasuk::count()   : 0,
            'jumlahSuratKeluar'   => class_exists(SuratKeluar::class)  ? SuratKeluar::count()  : 0,
            'jumlahDisposisi'     => class_exists(Disposisi::class)    ? Disposisi::count()    : 0,
        ];

        return view('pembuat-surat.dashboard', $data);
    }

}
