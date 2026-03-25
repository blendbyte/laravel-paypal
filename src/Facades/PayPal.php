<?php

namespace Blendbyte\PayPal\Facades;

/*
 * Class Facade
 * @package Blendbyte\PayPal\Facades
 * @see Blendbyte\PayPal\ExpressCheckout
 */

use Illuminate\Support\Facades\Facade;

class PayPal extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Blendbyte\PayPal\PayPalFacadeAccessor';
    }
}
