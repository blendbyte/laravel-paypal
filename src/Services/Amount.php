<?php

namespace Srmklive\PayPal\Services;

final class Amount
{
    /**
     * Currencies PayPal rejects decimal amounts for.
     *
     * @see https://developer.paypal.com/reference/currency-codes/
     */
    private const ZERO_DECIMAL_CURRENCIES = ['HUF', 'JPY', 'TWD'];

    /**
     * Format an amount as the decimal string PayPal expects for $currency,
     * rounded to the currency's precision (0 or 2 decimals).
     */
    public static function format(float $amount, string $currency): string
    {
        return number_format($amount, self::decimals($currency), '.', '');
    }

    public static function decimals(string $currency): int
    {
        return in_array(strtoupper($currency), self::ZERO_DECIMAL_CURRENCIES, true) ? 0 : 2;
    }
}
