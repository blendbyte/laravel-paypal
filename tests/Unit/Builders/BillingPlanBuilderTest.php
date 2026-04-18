<?php

use Srmklive\PayPal\Builders\BillingPlanBuilder;
use Srmklive\PayPal\Testing\MockPayPalClient;
use Srmklive\PayPal\Tests\MockResponsePayloads;

uses(MockResponsePayloads::class);

describe('BillingPlanBuilder', function () {

    // ── build() structure ─────────────────────────────────────────────────────

    it('builds a simple monthly plan', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('PROD-XXCD1234QWER65782')
            ->named('Basic Plan', 'A basic monthly plan')
            ->monthly(9.99)
            ->build();

        expect($plan['product_id'])->toBe('PROD-XXCD1234QWER65782');
        expect($plan['name'])->toBe('Basic Plan');
        expect($plan['description'])->toBe('A basic monthly plan');
        expect($plan['status'])->toBe('ACTIVE');
        expect($plan['billing_cycles'])->toHaveCount(1);

        $cycle = $plan['billing_cycles'][0];
        expect($cycle['tenure_type'])->toBe('REGULAR');
        expect($cycle['sequence'])->toBe(1);
        expect($cycle['total_cycles'])->toBe(0);
        expect($cycle['frequency']['interval_unit'])->toBe('MONTH');
        expect($cycle['frequency']['interval_count'])->toBe(1);
        expect($cycle['pricing_scheme']['fixed_price']['value'])->toBe('9.99');
        expect($cycle['pricing_scheme']['fixed_price']['currency_code'])->toBe('USD');
    });

    it('omits description when not provided', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('PROD-XXX')
            ->named('No Description Plan')
            ->monthly(5.00)
            ->build();

        expect($plan)->not->toHaveKey('description');
    });

    it('auto-sequences multiple cycles in addition order', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('PROD-XXX')
            ->named('Tiered Plan')
            ->trialMonthly(3.00, 2)
            ->trialMonthly(6.00, 3)
            ->monthly(10.00, 12)
            ->build();

        $cycles = $plan['billing_cycles'];
        expect($cycles)->toHaveCount(3);

        expect($cycles[0]['sequence'])->toBe(1);
        expect($cycles[0]['tenure_type'])->toBe('TRIAL');
        expect($cycles[0]['pricing_scheme']['fixed_price']['value'])->toBe('3.00');
        expect($cycles[0]['total_cycles'])->toBe(2);

        expect($cycles[1]['sequence'])->toBe(2);
        expect($cycles[1]['tenure_type'])->toBe('TRIAL');
        expect($cycles[1]['pricing_scheme']['fixed_price']['value'])->toBe('6.00');
        expect($cycles[1]['total_cycles'])->toBe(3);

        expect($cycles[2]['sequence'])->toBe(3);
        expect($cycles[2]['tenure_type'])->toBe('REGULAR');
        expect($cycles[2]['pricing_scheme']['fixed_price']['value'])->toBe('10.00');
        expect($cycles[2]['total_cycles'])->toBe(12);
    });

    it('formats prices to two decimal places', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('PROD-XXX')
            ->named('Plan')
            ->monthly(10.0)
            ->build();

        expect($plan['billing_cycles'][0]['pricing_scheme']['fixed_price']['value'])->toBe('10.00');
    });

    // ── Interval shortcuts ────────────────────────────────────────────────────

    it('daily() sets DAY interval', function () {
        $plan = BillingPlanBuilder::make()->forProduct('X')->named('P')->daily(1.00)->build();
        expect($plan['billing_cycles'][0]['frequency']['interval_unit'])->toBe('DAY');
    });

    it('weekly() sets WEEK interval', function () {
        $plan = BillingPlanBuilder::make()->forProduct('X')->named('P')->weekly(5.00)->build();
        expect($plan['billing_cycles'][0]['frequency']['interval_unit'])->toBe('WEEK');
    });

    it('annual() sets YEAR interval', function () {
        $plan = BillingPlanBuilder::make()->forProduct('X')->named('P')->annual(99.00)->build();
        expect($plan['billing_cycles'][0]['frequency']['interval_unit'])->toBe('YEAR');
    });

    it('trial shortcuts set TRIAL tenure_type', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->trialWeekly(2.00)
            ->trialAnnual(20.00)
            ->build();

        expect($plan['billing_cycles'][0]['tenure_type'])->toBe('TRIAL');
        expect($plan['billing_cycles'][0]['frequency']['interval_unit'])->toBe('WEEK');
        expect($plan['billing_cycles'][1]['tenure_type'])->toBe('TRIAL');
        expect($plan['billing_cycles'][1]['frequency']['interval_unit'])->toBe('YEAR');
    });

    it('trialDaily() defaults to totalCycles 1', function () {
        $plan = BillingPlanBuilder::make()->forProduct('X')->named('P')->trialDaily(0.00)->build();
        expect($plan['billing_cycles'][0]['total_cycles'])->toBe(1);
    });

    // ── regularCycle / trialCycle with custom intervals ───────────────────────

    it('regularCycle() accepts custom interval count', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->regularCycle('MONTH', 3, 25.00)
            ->build();

        expect($plan['billing_cycles'][0]['frequency']['interval_count'])->toBe(3);
        expect($plan['billing_cycles'][0]['frequency']['interval_unit'])->toBe('MONTH');
    });

    it('accepts lowercase interval unit', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->regularCycle('month', 1, 9.99)
            ->build();

        expect($plan['billing_cycles'][0]['frequency']['interval_unit'])->toBe('MONTH');
    });

    it('throws on invalid interval unit', function () {
        BillingPlanBuilder::make()->regularCycle('FORTNIGHT', 1, 9.99);
    })->throws(\InvalidArgumentException::class);

    it('throws on invalid interval unit in trialCycle', function () {
        BillingPlanBuilder::make()->trialCycle('QUARTER', 1, 9.99);
    })->throws(\InvalidArgumentException::class);

    // ── withSetupFee() ────────────────────────────────────────────────────────

    it('includes setup fee in payment_preferences', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->monthly(10.00)
            ->withSetupFee(5.00)
            ->build();

        $prefs = $plan['payment_preferences'];
        expect($prefs)->toHaveKey('setup_fee');
        expect($prefs['setup_fee']['value'])->toBe('5.00');
        expect($prefs['setup_fee']['currency_code'])->toBe('USD');
    });

    it('omits setup_fee from payment_preferences when not set', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->monthly(10.00)
            ->build();

        expect($plan['payment_preferences'])->not->toHaveKey('setup_fee');
    });

    it('setup_fee respects currency set via withCurrency()', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->withCurrency('EUR')
            ->monthly(10.00)
            ->withSetupFee(5.00)
            ->build();

        expect($plan['payment_preferences']['setup_fee']['currency_code'])->toBe('EUR');
    });

    it('withSetupFee accepts CANCEL_SUBSCRIPTION failure action', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->monthly(10.00)
            ->withSetupFee(5.00, 'CANCEL_SUBSCRIPTION')
            ->build();

        expect($plan['payment_preferences']['setup_fee_failure_action'])->toBe('CANCEL_SUBSCRIPTION');
    });

    it('throws on invalid setup fee failure action', function () {
        BillingPlanBuilder::make()->withSetupFee(5.00, 'INVALID');
    })->throws(\InvalidArgumentException::class);

    // ── withTax() ─────────────────────────────────────────────────────────────

    it('includes taxes when set', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->monthly(10.00)
            ->withTax(10.0)
            ->build();

        expect($plan)->toHaveKey('taxes');
        expect($plan['taxes']['percentage'])->toBe('10.00');
        expect($plan['taxes']['inclusive'])->toBeFalse();
    });

    it('withTax supports inclusive flag', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->monthly(10.00)
            ->withTax(7.5, true)
            ->build();

        expect($plan['taxes']['inclusive'])->toBeTrue();
        expect($plan['taxes']['percentage'])->toBe('7.50');
    });

    it('omits taxes key when not set', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->monthly(10.00)
            ->build();

        expect($plan)->not->toHaveKey('taxes');
    });

    // ── withFailureThreshold() ────────────────────────────────────────────────

    it('sets custom payment failure threshold', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->monthly(10.00)
            ->withFailureThreshold(5)
            ->build();

        expect($plan['payment_preferences']['payment_failure_threshold'])->toBe(5);
    });

    it('defaults payment_failure_threshold to 3', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->monthly(10.00)
            ->build();

        expect($plan['payment_preferences']['payment_failure_threshold'])->toBe(3);
    });

    // ── withCurrency() ────────────────────────────────────────────────────────

    it('uses custom currency in billing cycles', function () {
        $plan = BillingPlanBuilder::make()
            ->forProduct('X')->named('P')
            ->withCurrency('eur')
            ->monthly(10.00)
            ->build();

        expect($plan['billing_cycles'][0]['pricing_scheme']['fixed_price']['currency_code'])->toBe('EUR');
    });

    // ── make() factory ────────────────────────────────────────────────────────

    it('make() returns a new builder instance', function () {
        $a = BillingPlanBuilder::make();
        $b = BillingPlanBuilder::make();
        expect($a)->not->toBe($b);
    });

    // ── create() ─────────────────────────────────────────────────────────────

    it('create() calls createPlan on the provider with the built payload', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse($this->mockCreatePlansResponse());

        $provider = $mock->mockProvider();

        $response = BillingPlanBuilder::make()
            ->forProduct('PROD-XXCD1234QWER65782')
            ->named('Video Streaming Service Plan', 'Video Streaming Service basic plan')
            ->trialMonthly(3.00, 2)
            ->trialMonthly(6.00, 3)
            ->monthly(10.00, 12)
            ->withSetupFee(10.00)
            ->withTax(10.0)
            ->create($provider);

        expect($response)->toHaveKey('id', 'P-5ML4271244454362WXNWU5NQ');
        expect($mock->requestCount())->toBe(1);
        expect($mock->lastRequest()->getMethod())->toBe('POST');
        expect((string) $mock->lastRequest()->getUri())->toContain('v1/billing/plans');
    });
});
