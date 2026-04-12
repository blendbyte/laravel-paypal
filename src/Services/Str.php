<?php

namespace Srmklive\PayPal\Services;

use GuzzleHttp\Utils;

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
            Utils::jsonDecode($value, true, 512, 4194304);
        } catch (\InvalidArgumentException $jsonException) {
            return false;
        }

        return true;
    }
}
