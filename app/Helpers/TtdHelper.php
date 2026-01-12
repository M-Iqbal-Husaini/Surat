<?php

namespace App\Helpers;

class TtdHelper
{
    public static function url(?string $token): string
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('QR token tidak boleh kosong');
        }

        $ip   = config('app.ttd_ip');
        $port = config('app.ttd_port');

        return "http://{$ip}:{$port}/ttd/{$token}";
    }

}
