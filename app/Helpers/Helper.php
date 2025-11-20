<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

if (! function_exists('parseTimezone')) {
    function parseTimezone(?string $dateTime, string $timezone = 'Asia/Jakarta'): ?string
    {
        if (!$dateTime) {
            return null;
        }

        $tenant = request()->attributes->get('tenant');
        if ($tenant && isset($tenant->settings['timezone']) && !empty($tenant->settings['timezone'])) {
            $timezone = $tenant->settings['timezone'];
        }

        $parsed = Carbon::parse($dateTime)->setTimezone($timezone);
        return $parsed;
    }
}

if (!function_exists('sanitizeCurrency')) {
    function sanitizeCurrency($value)
    {
        // Replace thousand separators
        $value = str_replace('.', '', $value);

        // Convert decimal comma to dot
        $value = str_replace(',', '.', $value);

        // Remove all non-numeric characters except dot
        $value = preg_replace('/[^0-9.]/', '', $value);

        // If multiple dots exist, keep only the last one
        if (substr_count($value, '.') > 1) {
            $parts = explode('.', $value);
            $decimal = array_pop($parts);
            $value = implode('', $parts) . '.' . $decimal;
        }

        return (float) $value;
    }
}

if (!function_exists('convertCurrency')) {
    function convertCurrency($value, $toFormat = false, $decimal = 0, $prefix = '')
    {
        if ($toFormat) {
            // Format number to currency string (e.g. 29000.35 -> 29.000,35)
            return $prefix . number_format($value, $decimal, ',', '.');
        } else {
            // Convert currency string to number (e.g. 29.000,35 -> 29000.35)
            // Replace thousand separators
            $value = str_replace('.', '', $value);

            // Convert decimal comma to dot
            $value = str_replace(',', '.', $value);

            // Remove all non-numeric characters except dot
            $value = preg_replace('/[^0-9.]/', '', $value);

            // If multiple dots exist, keep only the last one
            if (substr_count($value, '.') > 1) {
                $parts = explode('.', $value);
                $decimal = array_pop($parts);
                $value = implode('', $parts) . '.' . $decimal;
            }

            return (float) $value;
        }
    }
}
if (!function_exists('splitParts')) {
    function splitParts($total, int $parts, int $precision = 2): array
    {
        $multiplier = pow(10, $precision);
        $scaledTotal = (int) round($total * $multiplier);
        $base = intdiv($scaledTotal, $parts);
        $remainder = $scaledTotal % $parts;

        $result = array_fill(0, $parts, $base);
        for ($i = 0; $i < $remainder; $i++) {
            $result[$i]++;
        }

        return array_map(function ($val) use ($multiplier) {
            return $val / $multiplier;
        }, $result);
    }
}
