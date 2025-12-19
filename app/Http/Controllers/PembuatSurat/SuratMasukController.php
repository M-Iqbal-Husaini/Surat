<?php

namespace App\Http\Controllers\PembuatSurat;

use App\Http\Controllers\Controller;

class SuratMasukController extends Controller
{
    public function index()
    {
        return view('pembuat-surat.surat-masuk.index');
    }
}
