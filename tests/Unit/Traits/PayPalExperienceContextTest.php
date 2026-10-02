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

it('setShippingAddressChangeCallback sets order_update_callback_config', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setShippingAddressChangeCallback('https://example.com/shipping-callback');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    expect(getContext($client)['order_update_callback_config'])->toBe([
        'callback_url' => 'https://example.com/shipping-callback',
        'callback_events' => ['SHIPPING_ADDRESS'],
    ]);
});

it('setShippingAddressChangeCallback accepts SHIPPING_OPTIONS events', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setShippingAddressChangeCallback('https://example.com/cb', ['SHIPPING_ADDRESS', 'SHIPPING_OPTIONS']);

    expect(getContext($client)['order_update_callback_config']['callback_events'])
        ->toBe(['SHIPPING_ADDRESS', 'SHIPPING_OPTIONS']);
});

it('setShippingAddressChangeCallback rejects empty or unknown events', function (array $events) {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect(fn () => $client->setShippingAddressChangeCallback('https://example.com/cb', $events))
        ->toThrow(InvalidArgumentException::class);
})->with([[[]], [['SHIPPING_METHOD']]]);

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
    expect($ctx['order_update_callback_config']['callback_url'])->toBe('https://example.com/shipping');
});

it('calling setBrandName twice overwrites the previous brand name', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setBrandName('First Brand');
    $client->setBrandName('Second Brand');

    expect(getContext($client)['brand_name'])->toBe('Second Brand');
});

// ---------------------------------------------------------------------------
// setStoredPaymentSource
// ---------------------------------------------------------------------------

function getStoredCredential(object $client): array
{
    return (new ReflectionProperty(PayPalClient::class, 'stored_credential'))->getValue($client);
}

it('setStoredPaymentSource stores the credential outside the experience context', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setStoredPaymentSource('CUSTOMER', 'ONE_TIME', 'IMMEDIATE');

    expect($result)->toBeInstanceOf(PayPalClient::class)
        ->and(getStoredCredential($client))->toBe([
            'payment_initiator' => 'CUSTOMER',
            'payment_type' => 'ONE_TIME',
            'usage_pattern' => 'IMMEDIATE',
        ])
        ->and(getContext($client))->not->toHaveKey('stored_payment_source');
});

it('setStoredPaymentSource adds usage when given', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource('MERCHANT', 'RECURRING', 'RECURRING_POSTPAID', usage: 'SUBSEQUENT');

    expect(getStoredCredential($client)['usage'])->toBe('SUBSEQUENT');
});

it('setStoredPaymentSource does not add previous_network_transaction_reference when previous_reference is false', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource('MERCHANT', 'RECURRING', 'DEFERRED', false);

    expect(getStoredCredential($client))->not->toHaveKey('previous_network_transaction_reference');
});

it('setStoredPaymentSource adds previous_network_transaction_reference when previous_reference is true', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource(
        'MERCHANT', 'RECURRING', 'RECURRING_PREPAID',
        true, 'TXN-001', '2024-01-15', 'ACQ-REF-001', 'VISA'
    );

    expect(getStoredCredential($client)['previous_network_transaction_reference'])->toBe([
        'id' => 'TXN-001',
        'date' => '2024-01-15',
        'acquirer_reference_number' => 'ACQ-REF-001',
        'network' => 'VISA',
    ]);
});

it('setStoredPaymentSource excludes null fields from previous_network_transaction_reference', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource('MERCHANT', 'RECURRING', 'RECURRING_PREPAID', true, 'TXN-002');

    expect(getStoredCredential($client)['previous_network_transaction_reference'])->toBe(['id' => 'TXN-002']);
});

it('setStoredPaymentSource omits the previous reference when no transaction id is given', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource('MERCHANT', 'RECURRING', 'RECURRING_PREPAID', true);

    expect(getStoredCredential($client))->not->toHaveKey('previous_network_transaction_reference');
});

it('setStoredPaymentSource passes values through unchanged for PayPal to validate', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setStoredPaymentSource('MERCHANT', 'RECURRING', 'RESUBMISSION');

    expect(getStoredCredential($client)['usage_pattern'])->toBe('RESUBMISSION');
});

it('setStoredPaymentSource and setBrandName do not affect each other', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $client->setBrandName('Acme Store');
    $client->setStoredPaymentSource('CUSTOMER', 'ONE_TIME', 'IMMEDIATE');
    $client->setBrandName('Acme Store 2');

    expect(getContext($client))->toBe(['brand_name' => 'Acme Store 2'])
        ->and(getStoredCredential($client)['payment_initiator'])->toBe('CUSTOMER');
});
