<?php

namespace Srmklive\PayPal\Services;

use GuzzleHttp\Utils;

class Str extends \Illuminate\Support\Str
{
    /**
     * Determine if a given value is valid JSON.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isJson($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        if (function_exists('json_validate')) {
            return json_validate($value);
        }

        Utils::jsonDecode($value, true, 512, JSON_THROW_ON_ERROR);

        return true;
    }
}
