<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Tests\MockRequestPayloads;
use Carbon\Carbon;

uses(MockRequestPayloads::class);

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('can extract capture id from a captured order response', function () {
    $response = $this->client->capturePaymentOrder('5O190127TN364715T');

    // Simulate the captured order response that capturePaymentOrder() returns.
    $captured = $this->mockOrderPaymentCapturedResponse();

    expect($this->client->getCaptureIdFromOrder($captured))->toBe('3C679366HH908993F');
});

it('returns null when order has no capture data', function () {
    $order = ['id' => '5O190127TN364715T', 'status' => 'CREATED'];

    expect($this->client->getCaptureIdFromOrder($order))->toBeNull();
});

it('can set shipping address change callback url', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setShippingAddressChangeCallback('https://example.com/shipping-callback');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockConfirmOrderResponse()
        )
    );

    $response = $this->client->setupOrderConfirmation('5O190127TN364715T', 'ORDER_COMPLETE_ON_PAYMENT_APPROVAL');

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});

it('can create an order with apple pay payment source', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreateOrdersResponse()));

    $this->client = $this->client->setPaymentSourceApplePay([
        'token' => ['id' => 'abc123', 'type' => 'APPLE_PAY'],
    ]);

    $response = $this->client->createOrderWithPaymentSource([
        'intent' => 'CAPTURE',
        'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]],
    ]);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});

it('can create an order with google pay payment source', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreateOrdersResponse()));

    $this->client = $this->client->setPaymentSourceGooglePay([
        'card' => ['name' => 'John Doe'],
    ]);

    $response = $this->client->createOrderWithPaymentSource([
        'intent' => 'CAPTURE',
        'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]],
    ]);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});

it('can create an order with venmo payment source', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreateOrdersResponse()));

    $this->client = $this->client->setPaymentSourceVenmo([
        'email_address' => 'venmo-user@example.com',
    ]);

    $response = $this->client->createOrderWithPaymentSource([
        'intent' => 'CAPTURE',
        'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]],
    ]);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});

it('can create an order with payment source and experience context', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreateOrdersResponse()));

    $this->client = $this->client
        ->setPaymentSourceApplePay(['token' => ['id' => 'abc123', 'type' => 'APPLE_PAY']])
        ->setReturnAndCancelUrl('https://example.com/success', 'https://example.com/cancel');

    $response = $this->client->createOrderWithPaymentSource([
        'intent' => 'CAPTURE',
        'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]],
    ]);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});

it('creates order without payment_source when none is set', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreateOrdersResponse()));

    $response = $this->client->createOrderWithPaymentSource([
        'intent' => 'CAPTURE',
        'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]],
    ]);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});

it('omits null fields from previous_network_transaction_reference', function () {
    // Regression: all four optional params were always included in the array,
    // so passing only some of them sent null values to PayPal (e.g. {"id":null}).
    $this->client->setStoredPaymentSource(
        'MERCHANT',
        'RECURRING',
        'RESUBMISSION',
        true,
        '5TY05013RG002845M', // id provided
        null,                 // date omitted
        null,                 // acquirer_reference_number omitted
        'VISA'                // network provided
    );

    $ctx = (new ReflectionClass($this->client))->getProperty('experience_context')->getValue($this->client);
    $ref = $ctx['stored_payment_source']['previous_network_transaction_reference'];

    expect($ref)->toHaveKey('id', '5TY05013RG002845M');
    expect($ref)->toHaveKey('network', 'VISA');
    expect($ref)->not->toHaveKey('date');
    expect($ref)->not->toHaveKey('acquirer_reference_number');
});

it('omits previous_network_transaction_reference entirely when previous_reference is false', function () {
    $this->client->setStoredPaymentSource('CUSTOMER', 'ONE_TIME', 'IMMEDIATE');

    $ctx   = (new ReflectionClass($this->client))->getProperty('experience_context')->getValue($this->client);
    $stored = $ctx['stored_payment_source'];

    expect($stored)->not->toHaveKey('previous_network_transaction_reference');
});

it('can confirm payment for an order', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->subDays(10)->toDateString();

    $this->client = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setBrandName('Test Brand')
        ->setStoredPaymentSource(
            'MERCHANT',
            'RECURRING',
            'RESUBMISSION',
            true,
            '5TY05013RG002845M',
            $start_date,
            'Invoice-005',
            'VISA'
        );

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockConfirmOrderResponse()
        )
    );

    $response = $this->client->setupOrderConfirmation('5O190127TN364715T', 'ORDER_COMPLETE_ON_PAYMENT_APPROVAL');

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});
