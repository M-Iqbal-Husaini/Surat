<?php

namespace App\Http\Controllers\SekretarisUnit;

use App\Http\Controllers\Controller;

class SuratMasukController extends Controller
{
    public function index()
    {
        return view('sekretaris-unit.surat-masuk.index');
    }
}
