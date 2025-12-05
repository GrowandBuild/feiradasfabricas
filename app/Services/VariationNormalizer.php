<?php

namespace App\Services;

class VariationNormalizer
{
    /**
     * Normalize hex string: ensure leading # and lowercase, or null
     */
    public function normalizeHex($h)
    {
        if ($h === null) return null;
        $h = trim((string)$h);
        if ($h === '') return null;
        if (strpos($h, '#') !== 0) $h = '#' . $h;
        return strtolower($h);
    }

    /**
     * Normalize a key into a sanitized lookup key
     */
    public function normalizeKey(string $k)
    {
        $k = trim((string)$k);
        if ($k === '') return $k;
        return preg_replace('/[^a-z0-9_]/', '_', mb_strtolower($k));
    }

    /**
     * Build a tolerant color -> hex lookup that includes multiple normalized forms
     * Accepts array like [ 'Blue' => '#00f', ... ]
     */
    public function buildNormalizedColorMap(array $colorHexMap): array
    {
        $out = [];
        foreach ($colorHexMap as $k => $v) {
            $hex = $this->normalizeHex($v);
            if (!$hex) continue;
            $trimmed = trim((string)$k);
            $lower = mb_strtolower($trimmed);
            $san = $this->normalizeKey($trimmed);
            $out[$trimmed] = $hex;
            $out[$lower] = $hex;
            $out[strtoupper($trimmed)] = $hex;
            $out[$san] = $hex;
            $out[strtoupper($san)] = $hex;
        }
        return $out;
    }

    /**
     * Attach hex values to an attribute groups array when color hex can be resolved.
     * Expects $attributeGroups['color'] to be a Collection or array of entries each with 'value'
     */
    public function attachHexToColorGroup($attributeGroups, array $normalizedColorMap)
    {
        if (!isset($attributeGroups['color'])) return $attributeGroups;
        $group = collect($attributeGroups['color'])->map(function ($entry) use ($normalizedColorMap) {
            $value = is_array($entry) ? ($entry['value'] ?? null) : (is_object($entry) ? ($entry->value ?? null) : null);
            if ($value !== null) {
                $keyForms = [trim((string)$value), mb_strtolower(trim((string)$value)), strtoupper(trim((string)$value)), $this->normalizeKey((string)$value), strtoupper($this->normalizeKey((string)$value))];
                foreach ($keyForms as $k) {
                    if (isset($normalizedColorMap[$k])) {
                        // ensure entry is array
                        if (!is_array($entry)) $entry = (array) $entry;
                        $entry['hex'] = $normalizedColorMap[$k];
                        break;
                    }
                }
            }
            return $entry;
        })->values();

        $attributeGroups['color'] = $group;
        return $attributeGroups;
    }
}
