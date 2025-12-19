<?php

namespace App\Http\Controllers\PembuatSurat;

use App\Http\Controllers\Controller;

class SuratKeluarController extends Controller
{
    public function index()
    {
        return view('pembuat-surat.surat-keluar.index');
    }
}
