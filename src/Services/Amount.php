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

    /**
     * Format a percentage (e.g. a tax rate) as a decimal string with at least
     * two and up to six decimals, without rounding typical rates such as 8.875.
     */
    public static function percentage(float $percentage): string
    {
        [$integer, $fraction] = explode('.', number_format($percentage, 6, '.', ''));

        return $integer.'.'.str_pad(rtrim($fraction, '0'), 2, '0');
    }

    public static function decimals(string $currency): int
    {
        return in_array(strtoupper($currency), self::ZERO_DECIMAL_CURRENCIES, true) ? 0 : 2;
    }
}
