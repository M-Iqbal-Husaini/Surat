<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratKeluarController extends Controller
{
    public function index()
    {
        // TODO: nanti ambil data surat keluar (semua) untuk monitoring admin
        return view('admin.surat-keluar.index');
    }
}
