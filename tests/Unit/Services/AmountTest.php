<?php

use Srmklive\PayPal\Services\Amount;

describe('Amount::format', function () {
    it('formats two-decimal currencies', function () {
        expect(Amount::format(9.99, 'USD'))->toBe('9.99')
            ->and(Amount::format(10, 'EUR'))->toBe('10.00')
            ->and(Amount::format(1234567.5, 'GBP'))->toBe('1234567.50');
    });

    it('rounds instead of truncating', function () {
        expect(Amount::format(9.999, 'USD'))->toBe('10.00')
            ->and(Amount::format(9.994, 'USD'))->toBe('9.99')
            ->and(Amount::format(0.29 * 3, 'USD'))->toBe('0.87');
    });

    it('formats zero-decimal currencies without decimals', function (string $currency) {
        expect(Amount::format(1000, $currency))->toBe('1000')
            ->and(Amount::format(999.6, $currency))->toBe('1000');
    })->with(['JPY', 'HUF', 'TWD']);

    it('treats currency codes case-insensitively', function () {
        expect(Amount::format(1000, 'jpy'))->toBe('1000')
            ->and(Amount::decimals('usd'))->toBe(2);
    });

    it('formats percentages with two to six decimals', function () {
        expect(Amount::percentage(10))->toBe('10.00')
            ->and(Amount::percentage(7.5))->toBe('7.50')
            ->and(Amount::percentage(8.875))->toBe('8.875')
            ->and(Amount::percentage(0.123456789))->toBe('0.123457');
    });

    it('handles floats PHP prints in scientific notation', function () {
        expect(Amount::format(1.0E-5, 'USD'))->toBe('0.00');
    });
});
