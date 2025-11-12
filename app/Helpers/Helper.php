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
