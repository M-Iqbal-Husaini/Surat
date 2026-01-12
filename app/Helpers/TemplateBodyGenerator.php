<?php

namespace App\Helpers;

use App\Models\JenisSurat;

class TemplateBodyGenerator
{
    public static function generate(string $kodeJenis): string
    {
        return match ($kodeJenis) {

            'AK' => '
<p>Dengan ini diterangkan bahwa:</p>
<table>
    <tr><td class="label">Nama</td><td class="separator">:</td><td class="value">{{nama_mahasiswa}}</td></tr>
    <tr><td class="label">NIM</td><td class="separator">:</td><td class="value">{{nim}}</td></tr>
    <tr><td class="label">Program Studi</td><td class="separator">:</td><td class="value">{{prodi}}</td></tr>
    <tr><td class="label">Semester</td><td class="separator">:</td><td class="value">{{semester}}</td></tr>
</table>
<p>{{keperluan}}</p>
',

            default => '
<p>Dengan hormat,</p>
<p>{{uraian}}</p>
',
        };
    }
}
