<?php

namespace App\Support;

class SispaqLocale
{
    public static function supported(): array
    {
        return config('sispaq-locale.supported', ['ms', 'en']);
    }

    public static function current(): string
    {
        return app()->getLocale();
    }

    public static function name(string $locale): string
    {
        return config("sispaq-locale.names.{$locale}", strtoupper($locale));
    }
}
