<?php

use Illuminate\Support\Facades\DB;

if (! function_exists('next_sequence')) {
    function next_sequence(string $code): string
    {
        return DB::transaction(function () use ($code) {
            $now = now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $day = $now->format('d');

            $sequence = DB::table('sequences')
                ->lockForUpdate()
                ->where('code', $code)
                ->where('active', true)
                ->first();

            if (! $sequence) {
                throw new Exception("Sequence with code '{$code}' not found or inactive.");
            }

            // Reset logic
            $shouldReset = match ($sequence->reset_period) {
                'year' => $sequence->year !== $year,
                'month' => $sequence->month !== $month || $sequence->year !== $year,
                'day' => $sequence->day !== $day || $sequence->month !== $month || $sequence->year !== $year,
                default => false,
            };

            $number = $shouldReset ? 1 : $sequence->number + 1;

            DB::table('sequences')
                ->where('id', $sequence->id)
                ->update([
                    'number' => $number,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'updated_at' => now(),
                ]);

            return format_sequence_pattern($sequence->pattern, [
                'PREFIX' => $sequence->prefix,
                'YYYY' => $year,
                'YY' => substr($year, -2),
                'MM' => $month,
                'DD' => $day,
                'SEQ' => str_pad($number, 4, '0', STR_PAD_LEFT),
                '####' => str_pad($number, 4, '0', STR_PAD_LEFT),
            ]);
        });
    }
}

if (! function_exists('format_sequence_pattern')) {
    function format_sequence_pattern(string $pattern, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $pattern = str_replace('{' . $key . '}', $value, $pattern);
        }

        // Clean double slashes or leftover braces
        return preg_replace('/[{}]+/', '', str_replace('//', '/', $pattern));
    }
}
