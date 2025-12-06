<?php

namespace App\Services;

use Illuminate\Support\Str;

class ProductNormalizer
{
    public static function normalizeName(string $raw): string
    {
        $s = trim($raw);
        $s = preg_replace('/\s+/', ' ', $s);
        $s = self::removeAccents($s);
        $s = mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
        return $s;
    }

    public static function removeAccents(string $text): string
    {
        $trans = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        if ($trans === false) {
            return $text;
        }
        return $trans;
    }

    public static function generateInternalId(): string
    {
        return (string) Str::uuid();
    }

    public static function normalizeGtin(?string $gtin): ?string
    {
        if (empty($gtin)) return null;
        $digits = preg_replace('/\D/', '', $gtin);
        if ($digits === '') return null;
        return $digits;
    }

    public static function validateGtin(string $digits): bool
    {
        $len = strlen($digits);
        if (!in_array($len, [8,12,13,14])) return false;
        $sum = 0;
        $parity = ($len % 2 === 0) ? 1 : 0;
        for ($i = 0; $i < $len - 1; $i++) {
            $n = (int) $digits[$i];
            $sum += ($i % 2 === $parity) ? $n * 3 : $n;
        }
        $check = (10 - ($sum % 10)) % 10;
        return $check === (int) $digits[$len - 1];
    }

    public static function computeVolumetricWeight(array $dimensions_cm): float
    {
        $l = isset($dimensions_cm['length']) ? (float)$dimensions_cm['length'] : 0.0;
        $w = isset($dimensions_cm['width']) ? (float)$dimensions_cm['width'] : 0.0;
        $h = isset($dimensions_cm['height']) ? (float)$dimensions_cm['height'] : 0.0;
        if ($l <= 0 || $w <= 0 || $h <= 0) return 0.0;
        return round(($l * $w * $h) / 6000, 3);
    }
}
