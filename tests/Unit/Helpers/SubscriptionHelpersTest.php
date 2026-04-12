<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

// ---------------------------------------------------------------------------
// Pure state setters
// ---------------------------------------------------------------------------

it('addProductById sets product state', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->addProductById('PROD-DEMO123');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $product = (new ReflectionProperty(PayPalClient::class, 'product'))->getValue($client);
    expect($product)->toBe(['id' => 'PROD-DEMO123']);
});

it('addBillingPlanById sets billing plan state', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->addBillingPlanById('P-PLAN123ABC');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan)->toBe(['id' => 'P-PLAN123ABC']);
});

it('addPaymentFailureThreshold sets threshold state', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->addPaymentFailureThreshold(5);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $threshold = (new ReflectionProperty(PayPalClient::class, 'payment_failure_threshold'))->getValue($client);
    expect($threshold)->toBe(5);
});

it('addShippingAddress sets shipping address state', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->addShippingAddress('John Doe', '123 Main St', 'Apt 4', 'New York', 'NY', '10001', 'US');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $address = (new ReflectionProperty(PayPalClient::class, 'shipping_address'))->getValue($client);
    expect($address)->toBe([
        'name'    => ['full_name' => 'John Doe'],
        'address' => [
            'address_line_1' => '123 Main St',
            'address_line_2' => 'Apt 4',
            'admin_area_2'   => 'New York',
            'admin_area_1'   => 'NY',
            'postal_code'    => '10001',
            'country_code'   => 'US',
        ],
    ]);
});

it('addTaxes sets taxes state', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->addTaxes(7.5);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $taxes = (new ReflectionProperty(PayPalClient::class, 'taxes'))->getValue($client);
    expect($taxes)->toBe(['percentage' => 7.5, 'inclusive' => false]);
});

it('addCustomId sets custom_id state', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->addCustomId('order-ref-001');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $custom_id = (new ReflectionProperty(PayPalClient::class, 'custom_id'))->getValue($client);
    expect($custom_id)->toBe('order-ref-001');
});

// ---------------------------------------------------------------------------
// addSetupFee — calls getCurrency()
// ---------------------------------------------------------------------------

it('addSetupFee sets has_setup_fee and payment preferences', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['getCurrency']);
    $client->method('getCurrency')->willReturn('USD');

    $result = $client->addSetupFee(9.99);

    expect($result)->toBeInstanceOf(PayPalClient::class);

    $has_setup_fee = (new ReflectionProperty(PayPalClient::class, 'has_setup_fee'))->getValue($client);
    expect($has_setup_fee)->toBeTrue();

    $prefs = (new ReflectionProperty(PayPalClient::class, 'payment_preferences'))->getValue($client);
    expect($prefs['auto_bill_outstanding'])->toBeTrue();
    expect($prefs['setup_fee'])->toBe(['value' => '9.99', 'currency_code' => 'USD']);
    expect($prefs['setup_fee_failure_action'])->toBe('CONTINUE');
});

// ---------------------------------------------------------------------------
// addPlanTrialPricing — calls addPlanBillingCycle() which calls getCurrency()
// ---------------------------------------------------------------------------

it('addPlanTrialPricing sets trial_pricing as TRIAL tenure type', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['getCurrency']);
    $client->method('getCurrency')->willReturn('USD');

    $result = $client->addPlanTrialPricing('DAY', 7, 0.00, 1);

    expect($result)->toBeInstanceOf(PayPalClient::class);

    $trial = (new ReflectionProperty(PayPalClient::class, 'trial_pricing'))->getValue($client);
    expect($trial['tenure_type'])->toBe('TRIAL');
    expect($trial['sequence'])->toBe(1);
    expect($trial['frequency'])->toBe(['interval_unit' => 'DAY', 'interval_count' => 7]);
    expect($trial['total_cycles'])->toBe(1);
    expect($trial['pricing_scheme']['fixed_price'])->toBe(['value' => '0.00', 'currency_code' => 'USD']);
});

// ---------------------------------------------------------------------------
// Guard conditions
// ---------------------------------------------------------------------------

it('setupSubscription throws when no billing plan is set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect(fn () => $client->setupSubscription('John', 'john@example.com'))
        ->toThrow(RuntimeException::class, 'No billing plan set');
});

it('addCustomPlan throws with invalid interval unit', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect(fn () => $client->addCustomPlan('Plan', 'Desc', 9.99, 'INVALID', 1))
        ->toThrow(RuntimeException::class, 'Billing intervals');
});

it('addMonthlyPlan throws when no product is set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    // $billing_plan is null (so the early-return guard is skipped) and $product is
    // also null — addBillingPlan() must throw because there is no product to attach.
    expect(fn () => $client->addMonthlyPlan('Monthly Plan', 'Test', 19.99))
        ->toThrow(RuntimeException::class, 'No product set');
});

// ---------------------------------------------------------------------------
// Idempotency — plan methods return early when billing_plan already set
// ---------------------------------------------------------------------------

it('addDailyPlan is idempotent when billing plan is already set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->addBillingPlanById('P-EXISTING123');

    $result = $client->addDailyPlan('Daily Plan', 'Test', 4.99);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan)->toBe(['id' => 'P-EXISTING123']);
});

it('addWeeklyPlan is idempotent when billing plan is already set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->addBillingPlanById('P-EXISTING123');

    $client->addWeeklyPlan('Weekly Plan', 'Test', 9.99);

    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan)->toBe(['id' => 'P-EXISTING123']);
});

it('addMonthlyPlan is idempotent when billing plan is already set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->addBillingPlanById('P-EXISTING123');

    $client->addMonthlyPlan('Monthly Plan', 'Test', 19.99);

    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan)->toBe(['id' => 'P-EXISTING123']);
});

it('addAnnualPlan is idempotent when billing plan is already set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->addBillingPlanById('P-EXISTING123');

    $client->addAnnualPlan('Annual Plan', 'Test', 99.99);

    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan)->toBe(['id' => 'P-EXISTING123']);
});

it('addCustomPlan is idempotent when billing plan is already set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->addBillingPlanById('P-EXISTING123');

    $client->addCustomPlan('Custom Plan', 'Test', 9.99, 'MONTH', 3);

    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan)->toBe(['id' => 'P-EXISTING123']);
});

// ---------------------------------------------------------------------------
// Plan creation — delegates to createPlan
// ---------------------------------------------------------------------------

it('addMonthlyPlan throws RuntimeException when createPlan returns an error response', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createPlan', 'getCurrency']);
    $client->method('getCurrency')->willReturn('USD');
    $client->method('createPlan')->willReturn([
        'error' => ['details' => [['description' => 'Plan creation failed']]],
    ]);

    $client->addProductById('PROD-123');

    expect(fn () => $client->addMonthlyPlan('Monthly Plan', 'Test', 19.99))
        ->toThrow(RuntimeException::class, 'Plan creation failed');
});

it('addMonthlyPlan throws RuntimeException when createPlan returns a non-array response', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createPlan', 'getCurrency']);
    $client->method('getCurrency')->willReturn('USD');
    $client->method('createPlan')->willReturn('unexpected-response');

    $client->addProductById('PROD-123');

    expect(fn () => $client->addMonthlyPlan('Monthly Plan', 'Test', 19.99))
        ->toThrow(RuntimeException::class, 'unexpected response format');
});

it('addMonthlyPlan creates a MONTH billing plan', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createPlan', 'getCurrency']);
    $client->method('getCurrency')->willReturn('USD');
    $client->expects($this->once())
        ->method('createPlan')
        ->willReturn(['id' => 'P-MONTHLY123', 'status' => 'ACTIVE']);

    $client->addProductById('PROD-123');
    $result = $client->addMonthlyPlan('Monthly Plan', 'Test plan', 19.99);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan['id'])->toBe('P-MONTHLY123');
});

it('addDailyPlan creates a DAY billing plan', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createPlan', 'getCurrency']);
    $client->method('getCurrency')->willReturn('USD');
    $client->expects($this->once())
        ->method('createPlan')
        ->willReturn(['id' => 'P-DAILY123', 'status' => 'ACTIVE']);

    $client->addProductById('PROD-123');
    $result = $client->addDailyPlan('Daily Plan', 'Test plan', 4.99);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan['id'])->toBe('P-DAILY123');
});

it('addCustomPlan creates a billing plan with the given interval', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createPlan', 'getCurrency']);
    $client->method('getCurrency')->willReturn('USD');
    $client->expects($this->once())
        ->method('createPlan')
        ->willReturn(['id' => 'P-CUSTOM123', 'status' => 'ACTIVE']);

    $client->addProductById('PROD-123');
    $result = $client->addCustomPlan('Custom Plan', 'Test plan', 14.99, 'WEEK', 2);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $billing_plan = (new ReflectionProperty(PayPalClient::class, 'billing_plan'))->getValue($client);
    expect($billing_plan['id'])->toBe('P-CUSTOM123');
});

it('addMonthlyPlan includes trial cycle when trial pricing is set', function () {
    $capturedParams = null;
    $client = $this->createPartialMock(PayPalClient::class, ['createPlan', 'getCurrency']);
    $client->method('getCurrency')->willReturn('USD');
    $client->expects($this->once())
        ->method('createPlan')
        ->willReturnCallback(function (array $params) use (&$capturedParams) {
            $capturedParams = $params;

            return ['id' => 'P-TRIAL123', 'status' => 'ACTIVE'];
        });

    $client->addProductById('PROD-123');
    $client->addPlanTrialPricing('DAY', 7);
    $client->addMonthlyPlan('Monthly with Trial', 'Test', 19.99);

    expect($capturedParams['billing_cycles'])->toHaveCount(2);
    expect($capturedParams['billing_cycles'][0]['tenure_type'])->toBe('TRIAL');
    expect($capturedParams['billing_cycles'][1]['tenure_type'])->toBe('REGULAR');
});

// ---------------------------------------------------------------------------
// addProduct — delegates to createProduct
// ---------------------------------------------------------------------------

it('addProduct sets product from API response', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createProduct']);
    $client->expects($this->once())
        ->method('createProduct')
        ->willReturn(['id' => 'PROD-API123', 'name' => 'Demo Product']);

    $result = $client->addProduct('Demo Product', 'A demo', 'SERVICE', 'SOFTWARE');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $product = (new ReflectionProperty(PayPalClient::class, 'product'))->getValue($client);
    expect($product['id'])->toBe('PROD-API123');
});

it('addProduct throws RuntimeException when createProduct returns an error response', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createProduct']);
    $client->method('createProduct')->willReturn([
        'error' => ['details' => [['description' => 'Product creation failed']]],
    ]);

    expect(fn () => $client->addProduct('Demo', 'Desc', 'SERVICE', 'SOFTWARE'))
        ->toThrow(RuntimeException::class, 'Product creation failed');
});

it('addProduct throws RuntimeException when createProduct returns a non-array response', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createProduct']);
    $client->method('createProduct')->willReturn('unexpected-response');

    expect(fn () => $client->addProduct('Demo', 'Desc', 'SERVICE', 'SOFTWARE'))
        ->toThrow(RuntimeException::class, 'unexpected response format');
});

it('addProduct is idempotent when product is already set', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createProduct']);
    $client->expects($this->never())->method('createProduct');
    $client->addProductById('PROD-EXISTING');

    $result = $client->addProduct('Demo Product', 'A demo', 'SERVICE', 'SOFTWARE');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $product = (new ReflectionProperty(PayPalClient::class, 'product'))->getValue($client);
    expect($product)->toBe(['id' => 'PROD-EXISTING']);
});
