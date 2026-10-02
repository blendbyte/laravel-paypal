<?php

namespace Srmklive\PayPal\Services;

class Str extends \Illuminate\Support\Str
{
    /**
     * Determine if a given value is valid JSON.
     *
     * @param  mixed  $value
     */
    public static function isJson($value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        if (function_exists('json_validate')) {
            return json_validate($value, 512);
        }

        try {
            json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return false;
        }

        return true;
    }
}
