<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

it('finds eligible payment methods', function () {
    $eligible = ['eligible_methods' => ['venmo' => ['can_be_vaulted' => true]]];
    $this->mock->addResponse($eligible);

    $data = [
        'customer' => ['country_code' => 'US'],
        'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '100.00']]],
        'preferences' => ['payment_source_constraint' => ['constraint_type' => 'EXCLUDE', 'payment_sources' => ['PAYPAL']]],
    ];

    $response = $this->client->findEligiblePaymentMethods($data);

    $request = $this->mock->lastRequest();
    expect($response)->toBe($eligible)
        ->and($request->getMethod())->toBe('POST')
        ->and((string) $request->getUri())->toEndWith('/v2/payments/find-eligible-methods')
        ->and(json_decode((string) $request->getBody(), true))->toBe($data);
});

it('sends an empty JSON object when called without data', function () {
    $this->mock->addResponse(['eligible_methods' => []]);

    $this->client->findEligiblePaymentMethods();

    expect((string) $this->mock->lastRequest()->getBody())->toBe('{}');
});

it('returns PayPal errors when finding eligible payment methods fails', function () {
    $this->mock->addResponse(['name' => 'INVALID_REQUEST'], 400);

    expect($this->client->findEligiblePaymentMethods())->toBe(['error' => ['name' => 'INVALID_REQUEST']]);
});
