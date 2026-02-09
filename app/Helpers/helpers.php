<?php

use Illuminate\Support\Carbon;

if (!function_exists('format_decimal')) {
    /**
     * Formatea un valor a decimal basado en la configuración de la API.
     */
    function format_decimal($value): float
    {
        return (float) number_format(
            (float) $value,
            config('api.defaults.decimal', 2),
            config('api.defaults.decimal_separator', '.'),
            ''
        );
    }
}

if (!function_exists('format_date')) {
    /**
     * Formatea una instancia de Carbon según la configuración de la API.
     */
    function format_date($value): ?string
    {
        if (!$value instanceof Carbon) {
            return null;
        }
        return $value->format(config('api.defaults.format_date_time', 'Y-m-d H:i:s'));
    }
}
