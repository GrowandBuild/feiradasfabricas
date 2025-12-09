<?php

namespace App\Services;

/**
 * VariationNormalizer: neutralized stub during complete removal of variations/attributes subsystem.
 * Kept minimal to avoid fatal errors in code paths that may still reference it.
 */
class VariationNormalizer
{
    public function normalizeHex($h)
    {
        if ($h === null) return null;
        $h = trim((string)$h);
        if ($h === '') return null;
        if (strpos($h, '#') !== 0) $h = '#' . $h;
        return strtolower($h);
    }

    public function buildNormalizedColorMap(array $colorHexMap): array
    {
        $out = [];
        foreach ($colorHexMap as $k => $v) {
            $hex = $this->normalizeHex($v);
            if ($hex) $out[$k] = $hex;
        }
        return $out;
    }

    public function attachHexToColorGroup($attributeGroups, array $normalizedColorMap)
    {
        // No-op in cleanup mode: return attribute groups unchanged.
        return $attributeGroups;
    }
}
