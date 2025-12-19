<?php

namespace App\Http\Controllers\SekretarisUnit;

use App\Http\Controllers\Controller;

class SuratKeluarController extends Controller
{
    public function index()
    {
        return view('sekretaris-unit.surat-keluar.index');
    }
}
