<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

// Helper: read the protected $experience_context property.
function getContext(object $client): array
{
    return (new ReflectionProperty(PayPalClient::class, 'experience_context'))->getValue($client);
}

// ---------------------------------------------------------------------------
// setBrandName
// ---------------------------------------------------------------------------

it('setBrandName sets brand_name in experience_context', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setBrandName('Acme Store');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect(getContext($client)['brand_name'])->toBe('Acme Store');
});

// ---------------------------------------------------------------------------
// setReturnAndCancelUrl
// ---------------------------------------------------------------------------

it('setReturnAndCancelUrl sets return_url and cancel_url', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setReturnAndCancelUrl('https://example.com/success', 'https://example.com/cancel');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $ctx = getContext($client);
    expect($ctx['return_url'])->toBe('https://example.com/success');
    expect($ctx['cancel_url'])->toBe('https://example.com/cancel');
});

// ---------------------------------------------------------------------------
// setShippingAddressChangeCallback
// ---------------------------------------------------------------------------

it('setShippingAddressChangeCallback sets the callback URL', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setShippingAddressChangeCallback('https://example.com/shipping-callback');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect(getContext($client)['shipping_address_change_callback_url'])->toBe('https://example.com/shipping-callback');
});

// ---------------------------------------------------------------------------
// Fluent chaining — array_merge accumulates context across calls
// ---------------------------------------------------------------------------

it('chaining multiple setters accumulates context keys without overwriting', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client
        ->setBrandName('Acme Store')
        ->setReturnAndCancelUrl('https://example.com/success', 'https://example.com/cancel')
        ->setShippingAddressChangeCallback('https://example.com/shipping');

    $ctx = getContext($client);

    expect($ctx['brand_name'])->toBe('Acme Store');
    expect($ctx['return_url'])->toBe('https://example.com/success');
    expect($ctx['cancel_url'])->toBe('https://example.com/cancel');
    expect($ctx['shipping_address_change_callback_url'])->toBe('https://example.com/shipping');
});

it('calling setBrandName twice overwrites the previous brand name', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setBrandName('First Brand');
    $client->setBrandName('Second Brand');

    expect(getContext($client)['brand_name'])->toBe('Second Brand');
});

// ---------------------------------------------------------------------------
// setStoredPaymentSource — without previous reference
// ---------------------------------------------------------------------------

it('setStoredPaymentSource sets payment initiator, type and usage pattern', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setStoredPaymentSource('CUSTOMER', 'ONE_TIME', 'IMMEDIATE');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $sps = getContext($client)['stored_payment_source'];
    expect($sps['payment_initiator'])->toBe('CUSTOMER');
    expect($sps['payment_type'])->toBe('ONE_TIME');
    expect($sps['usage_pattern'])->toBe('IMMEDIATE');
});

it('setStoredPaymentSource does not add previous_network_transaction_reference when previous_reference is false', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource('MERCHANT', 'RECURRING', 'DEFERRED', false);

    $sps = getContext($client)['stored_payment_source'];
    expect($sps)->not->toHaveKey('previous_network_transaction_reference');
});

// ---------------------------------------------------------------------------
// setStoredPaymentSource — with previous reference
// ---------------------------------------------------------------------------

it('setStoredPaymentSource adds previous_network_transaction_reference when previous_reference is true', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource(
        'MERCHANT', 'RECURRING', 'RESUBMISSION',
        true, 'TXN-001', '2024-01-15', 'ACQ-REF-001', 'VISA'
    );

    $sps = getContext($client)['stored_payment_source'];
    $ref = $sps['previous_network_transaction_reference'];

    expect($ref['id'])->toBe('TXN-001');
    expect($ref['date'])->toBe('2024-01-15');
    expect($ref['acquirer_reference_number'])->toBe('ACQ-REF-001');
    expect($ref['network'])->toBe('VISA');
});

it('setStoredPaymentSource excludes null fields from previous_network_transaction_reference via array_filter', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    // Pass only transaction_id; leave date, acquirer_reference_number, and network as null.
    $client->setStoredPaymentSource(
        'MERCHANT', 'RECURRING', 'RESUBMISSION',
        true, 'TXN-002', null, null, null
    );

    $ref = getContext($client)['stored_payment_source']['previous_network_transaction_reference'];

    expect($ref)->toHaveKey('id');
    expect($ref)->not->toHaveKey('date');
    expect($ref)->not->toHaveKey('acquirer_reference_number');
    expect($ref)->not->toHaveKey('network');
});

it('setStoredPaymentSource produces an empty previous_network_transaction_reference when all reference fields are null', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource('MERCHANT', 'RECURRING', 'RESUBMISSION', true);

    $ref = getContext($client)['stored_payment_source']['previous_network_transaction_reference'];

    expect($ref)->toBe([]);
});

// ---------------------------------------------------------------------------
// setStoredPaymentSource coexists with other context keys (array_merge)
// ---------------------------------------------------------------------------

it('setStoredPaymentSource does not overwrite previously set brand name', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setBrandName('Acme Store');
    $client->setStoredPaymentSource('CUSTOMER', 'ONE_TIME', 'IMMEDIATE');

    $ctx = getContext($client);
    expect($ctx['brand_name'])->toBe('Acme Store');
    expect($ctx['stored_payment_source']['payment_initiator'])->toBe('CUSTOMER');
});

it('setBrandName does not overwrite a previously set stored_payment_source', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource('MERCHANT', 'RECURRING', 'DEFERRED');
    $client->setBrandName('Acme Store');

    $ctx = getContext($client);
    expect($ctx['stored_payment_source']['payment_type'])->toBe('RECURRING');
    expect($ctx['brand_name'])->toBe('Acme Store');
});
