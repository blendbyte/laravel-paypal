<?php

namespace Srmklive\PayPal;

use Srmklive\PayPal\Services\PayPal;
use Exception;

class PayPalFacadeAccessor
{
    /**
     * PayPal API provider object.
     *
     * @var PayPal|null
     */
    public static $provider;

    /**
     * Get specific PayPal API provider object to use.
     *
     *
     * @return PayPal|null
     *
     * @throws Exception
     */
    public static function getProvider(): ?PayPal
    {
        return self::$provider;
    }

    /**
     * Set PayPal API Client to use.
     *
     *
     * @return PayPal
     *
     * @throws Exception
     */
    public static function setProvider(): PayPal
    {
        $provider = new PayPal;
        self::$provider = $provider;

        return $provider;
    }
}
