<?php

namespace App\Helpers;

use App\Models\TemplateSurat;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Surat;

class TemplateRenderer
{
    /**
     * Render template surat dengan data (real / preview)
     */
    public static function render(Surat $surat): string
    {
        $template = $surat->template;

        if (!$template) {
            return '';
        }

        $html = $template->body_html;

        foreach ($surat->data_json ?? [] as $key => $value) {
            $html = str_replace(
                ['{{'.$key.'}}', '{{ '.$key.' }}'],
                nl2br(e($value)),
                $html
            );
        }

        return $html;
    }

    /**
     * Format value sesuai tipe field
     */
    protected static function formatValue(mixed $value, string $type): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return match ($type) {
            'textarea' => nl2br(e($value)),
            'date'     => self::formatDate($value),
            'number'   => e(number_format((int) $value, 0, ',', '.')),
            default    => e($value),
        };
    }

    /**
     * Format tanggal aman
     */
    protected static function formatDate(mixed $value): string
    {
        try {
            return Carbon::parse($value)->translatedFormat('d F Y');
        } catch (\Exception $e) {
            return e($value);
        }
    }

    /**
     * Dummy value untuk preview surat
     */
    protected static function dummyValue($field): string
    {
        return match ($field->type) {
            'text'     => $field->label,
            'textarea' => 'Contoh ' . Str::lower($field->label),
            'date'     => now()->translatedFormat('d F Y'),
            'number'   => '100',
            default    => 'Contoh ' . $field->label,
        };
    }
}
