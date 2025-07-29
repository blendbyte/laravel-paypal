<?php

namespace Srmklive\PayPal;

use Exception;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalFacadeAccessor
{
    /**
     * PayPal API provider object.
     *
     * @var \Srmklive\PayPal\Services\PayPal
     */
    public static $provider;

    /**
     * Get specific PayPal API provider object to use.
     *
     * @throws Exception
     *
     * @return \Srmklive\PayPal\Services\PayPal
     */
    public static function getProvider()
    {
        return self::$provider;
    }

    /**
     * Set PayPal API Client to use.
     *
     * @throws \Exception
     *
     * @return \Srmklive\PayPal\Services\PayPal
     */
    public static function setProvider()
    {
        // Set default provider. Defaults to ExpressCheckout
        self::$provider = new PayPalClient();

        return self::getProvider();
    }
}
