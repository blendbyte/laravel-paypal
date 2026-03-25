<?php

namespace Blendbyte\PayPal;

use Blendbyte\PayPal\Services\PayPal;
use Blendbyte\PayPal\Services\PayPal as PayPalClient;
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
     * @return PayPal
     *
     * @throws Exception
     */
    public static function getProvider()
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
    public static function setProvider()
    {
        // Set default provider. Defaults to ExpressCheckout
        self::$provider = new PayPalClient;

        return self::getProvider();
    }
}
