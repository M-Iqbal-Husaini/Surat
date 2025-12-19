<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratMasukController extends Controller
{
    public function index()
    {
        return view('verifikator.surat-keluar.index');
    }
}
