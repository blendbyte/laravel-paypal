<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

// ---------------------------------------------------------------------------
// Payment source setters — verified via getPaymentSource()
// ---------------------------------------------------------------------------

it('setPaymentSourceCard sets card as payment source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $data   = ['number' => '4111111111111111', 'expiry' => '2027-02'];

    $result = $client->setPaymentSourceCard($data);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect($client->getPaymentSource())->toBe(['card' => $data]);
});

it('setPaymentSourcePayPal sets paypal as payment source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $data   = ['description' => 'PayPal Checkout'];

    $result = $client->setPaymentSourcePayPal($data);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect($client->getPaymentSource())->toBe(['paypal' => $data]);
});

it('setPaymentSourceVenmo sets venmo as payment source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $data   = ['description' => 'Venmo Checkout'];

    $result = $client->setPaymentSourceVenmo($data);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect($client->getPaymentSource())->toBe(['venmo' => $data]);
});

it('setPaymentSourceApplePay sets apple_pay as payment source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $data   = ['token' => ['id' => 'tok_applepay', 'type' => 'apple_pay']];

    $result = $client->setPaymentSourceApplePay($data);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect($client->getPaymentSource())->toBe(['apple_pay' => $data]);
});

it('setPaymentSourceGooglePay sets google_pay as payment source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $data   = ['decrypted_token' => ['message_id' => 'msg-123']];

    $result = $client->setPaymentSourceGooglePay($data);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect($client->getPaymentSource())->toBe(['google_pay' => $data]);
});

it('setPaymentSourcePayUponInvoice sets pay_upon_invoice as payment source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $data   = [
        'name'            => ['given_name' => 'John', 'surname' => 'Doe'],
        'email'           => 'john.doe@example.com',
        'birth_date'      => '1990-01-01',
        'phone'           => ['country_code' => '49', 'national_number' => '1234567890'],
        'billing_address' => ['address_line_1' => 'Hauptstraße 1', 'admin_area_2' => 'Berlin', 'postal_code' => '10115', 'country_code' => 'DE'],
    ];

    $result = $client->setPaymentSourcePayUponInvoice($data);

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect($client->getPaymentSource())->toBe(['pay_upon_invoice' => $data]);
});

it('setTokenSource sets token as payment source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setTokenSource('tok-abc123', 'BILLING_AGREEMENT');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect($client->getPaymentSource())->toBe([
        'token' => ['id' => 'tok-abc123', 'type' => 'BILLING_AGREEMENT'],
    ]);
});

it('getPaymentSource returns empty array when no source has been set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect($client->getPaymentSource())->toBe([]);
});

// ---------------------------------------------------------------------------
// Customer source setters
// ---------------------------------------------------------------------------

it('setCustomerSource sets customer_source state', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setCustomerSource('customer_4029352050');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $customer = (new ReflectionProperty(PayPalClient::class, 'customer_source'))->getValue($client);
    expect($customer)->toBe(['id' => 'customer_4029352050']);
});

it('setCustomerId is an alias for setCustomerSource', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setCustomerId('customer_4029352050');

    $customer = (new ReflectionProperty(PayPalClient::class, 'customer_source'))->getValue($client);
    expect($customer)->toBe(['id' => 'customer_4029352050']);
});

// ---------------------------------------------------------------------------
// Card billing address — merges into payment_source.card
// ---------------------------------------------------------------------------

it('setCardBillingAddress merges billing address into card payment source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->setPaymentSourceCard(['number' => '4111111111111111']);

    $result = $client->setCardBillingAddress('123 Main St', 'New York', 'NY', '10001', 'US');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $source = $client->getPaymentSource();
    expect($source['card']['billing_address'])->toBe([
        'address_line_1' => '123 Main St',
        'admin_area_2'   => 'New York',
        'admin_area_1'   => 'NY',
        'postal_code'    => '10001',
        'country_code'   => 'US',
    ]);
    expect($source['card']['number'])->toBe('4111111111111111');
});

it('setCardBillingAddress omits empty address_line_2', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setCardBillingAddress('123 Main St', 'New York', 'NY', '10001', 'US', '');

    $source = $client->getPaymentSource();
    expect($source['card']['billing_address'])->not->toHaveKey('address_line_2');
});

it('setCardBillingAddress includes non-empty address_line_2', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setCardBillingAddress('123 Main St', 'New York', 'NY', '10001', 'US', 'Suite 500');

    $source = $client->getPaymentSource();
    expect($source['card']['billing_address']['address_line_2'])->toBe('Suite 500');
});

// ---------------------------------------------------------------------------
// Card vaulting and verification — merge into payment_source.card.attributes
// ---------------------------------------------------------------------------

it('setCardVaulting sets vault store_in_vault in card attributes', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setCardVaulting('ON_SUCCESS');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $source = $client->getPaymentSource();
    expect($source['card']['attributes']['vault']['store_in_vault'])->toBe('ON_SUCCESS');
});

it('setCardVerification sets verification method in card attributes', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setCardVerification('SCA_ALWAYS');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $source = $client->getPaymentSource();
    expect($source['card']['attributes']['verification']['method'])->toBe('SCA_ALWAYS');
});

it('setCardVaulting and setCardVerification can be combined without overwriting each other', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setCardVaulting('ON_SUCCESS');
    $client->setCardVerification('SCA_WHEN_REQUIRED');

    $source     = $client->getPaymentSource();
    $attributes = $source['card']['attributes'];

    expect($attributes['vault']['store_in_vault'])->toBe('ON_SUCCESS');
    expect($attributes['verification']['method'])->toBe('SCA_WHEN_REQUIRED');
});

// ---------------------------------------------------------------------------
// sendPaymentMethodRequest — delegates to createPaymentSourceToken / createPaymentSetupToken
// ---------------------------------------------------------------------------

it('sendPaymentMethodRequest calls createPaymentSourceToken when create_source is false', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createPaymentSourceToken', 'createPaymentSetupToken']);
    $client->setPaymentSourcePayPal(['description' => 'Test']);

    $client->expects($this->once())
        ->method('createPaymentSourceToken')
        ->willReturn(['id' => 'token-123']);

    $client->expects($this->never())->method('createPaymentSetupToken');

    $result = $client->sendPaymentMethodRequest(false);

    expect($result)->toBe(['id' => 'token-123']);
});

it('sendPaymentMethodRequest calls createPaymentSetupToken when create_source is true', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createPaymentSourceToken', 'createPaymentSetupToken']);
    $client->setPaymentSourcePayPal(['description' => 'Test']);

    $client->expects($this->never())->method('createPaymentSourceToken');

    $client->expects($this->once())
        ->method('createPaymentSetupToken')
        ->willReturn(['id' => 'setup-token-123']);

    $result = $client->sendPaymentMethodRequest(true);

    expect($result)->toBe(['id' => 'setup-token-123']);
});

it('sendPaymentMethodRequest includes customer in payload when customer source is set', function () {
    $capturedPayload = null;
    $client          = $this->createPartialMock(PayPalClient::class, ['createPaymentSourceToken']);
    $client->setPaymentSourceCard(['number' => '4111111111111111']);
    $client->setCustomerId('customer_4029352050');

    $client->expects($this->once())
        ->method('createPaymentSourceToken')
        ->willReturnCallback(function (array $payload) use (&$capturedPayload) {
            $capturedPayload = $payload;

            return ['id' => 'token-456'];
        });

    $client->sendPaymentMethodRequest(false);

    expect($capturedPayload)->toHaveKey('payment_source');
    expect($capturedPayload)->toHaveKey('customer');
    expect($capturedPayload['customer'])->toBe(['id' => 'customer_4029352050']);
});

it('sendPaymentMethodRequest omits customer key when no customer source is set', function () {
    $capturedPayload = null;
    $client          = $this->createPartialMock(PayPalClient::class, ['createPaymentSourceToken']);
    $client->setPaymentSourceCard(['number' => '4111111111111111']);

    $client->expects($this->once())
        ->method('createPaymentSourceToken')
        ->willReturnCallback(function (array $payload) use (&$capturedPayload) {
            $capturedPayload = $payload;

            return ['id' => 'token-789'];
        });

    $client->sendPaymentMethodRequest(false);

    expect($capturedPayload)->toHaveKey('payment_source');
    expect($capturedPayload)->not->toHaveKey('customer');
});
