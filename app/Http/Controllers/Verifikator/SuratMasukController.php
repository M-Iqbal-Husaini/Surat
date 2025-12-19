<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;

class SuratMasukController extends Controller
{
    public function index()
    {
        return view('verifikator.surat-masuk.index');
    }
}
