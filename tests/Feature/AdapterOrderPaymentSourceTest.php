<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
    $this->order = [
        'intent' => 'CAPTURE',
        'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]],
    ];
});

function sentBody(MockPayPalClient $mock): array
{
    return json_decode((string) $mock->lastRequest()->getBody(), true);
}

it('sends the order update callback config in the paypal experience context', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->setShippingAddressChangeCallback('https://example.com/cb', ['SHIPPING_ADDRESS', 'SHIPPING_OPTIONS'])
        ->createOrderWithPaymentSource($this->order);

    expect(sentBody($this->mock)['payment_source']['paypal']['experience_context']['order_update_callback_config'])->toBe([
        'callback_url' => 'https://example.com/cb',
        'callback_events' => ['SHIPPING_ADDRESS', 'SHIPPING_OPTIONS'],
    ]);
});

it('sends the stored credential for card payment sources', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->setPaymentSourceCard(['vault_id' => 'VAULT-1'])
        ->setStoredPaymentSource('MERCHANT', 'RECURRING', 'RECURRING_POSTPAID', true, 'TXN-1', null, null, 'VISA', 'SUBSEQUENT')
        ->createOrderWithPaymentSource($this->order);

    expect(sentBody($this->mock)['payment_source']['card'])->toBe([
        'vault_id' => 'VAULT-1',
        'stored_credential' => [
            'payment_initiator' => 'MERCHANT',
            'payment_type' => 'RECURRING',
            'usage' => 'SUBSEQUENT',
            'previous_network_transaction_reference' => ['id' => 'TXN-1', 'network' => 'VISA'],
        ],
    ]);
});

it('sends the stored credential for paypal payment sources', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->setPaymentSourcePayPal(['vault_id' => 'VAULT-1'])
        ->setStoredPaymentSource('MERCHANT', 'RECURRING', 'RECURRING_POSTPAID', usage: 'SUBSEQUENT')
        ->createOrderWithPaymentSource($this->order);

    expect(sentBody($this->mock)['payment_source']['paypal'])->toBe([
        'vault_id' => 'VAULT-1',
        'stored_credential' => [
            'payment_initiator' => 'MERCHANT',
            'usage_pattern' => 'RECURRING_POSTPAID',
            'usage' => 'SUBSEQUENT',
        ],
    ]);
});

it('does not send a stored credential for payment sources without support', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->setPaymentSourceVenmo(['email_address' => 'buyer@example.com'])
        ->setStoredPaymentSource('CUSTOMER', 'ONE_TIME', 'IMMEDIATE')
        ->createOrderWithPaymentSource($this->order);

    expect(sentBody($this->mock)['payment_source']['venmo'])->toBe(['email_address' => 'buyer@example.com']);
});

it('never sends stored_payment_source inside the experience context', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->setBrandName('Acme')
        ->setStoredPaymentSource('CUSTOMER', 'ONE_TIME', 'IMMEDIATE')
        ->createOrderWithPaymentSource($this->order);

    expect(sentBody($this->mock)['payment_source']['paypal']['experience_context'])->toBe(['brand_name' => 'Acme']);
});

it('only sends the experience context fields each payment source supports', function (string $setter, array $data, string $key, ?array $expected) {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->{$setter}($data)
        ->setBrandName('Acme')
        ->setReturnAndCancelUrl('https://example.com/ok', 'https://example.com/cancel')
        ->setShippingAddressChangeCallback('https://example.com/cb')
        ->createOrderWithPaymentSource($this->order);

    $source = sentBody($this->mock)['payment_source'][$key];

    if ($expected === null) {
        expect($source)->not->toHaveKey('experience_context');
    } else {
        expect(array_keys($source['experience_context']))->toBe($expected);
    }
})->with([
    'paypal' => ['setPaymentSourcePayPal', ['vault_id' => 'V-1'], 'paypal', ['brand_name', 'return_url', 'cancel_url', 'order_update_callback_config']],
    'card' => ['setPaymentSourceCard', ['vault_id' => 'V-1'], 'card', ['return_url', 'cancel_url']],
    'apple pay' => ['setPaymentSourceApplePay', ['id' => 'A-1'], 'apple_pay', ['return_url', 'cancel_url']],
    'google pay' => ['setPaymentSourceGooglePay', ['card' => ['name' => 'X']], 'google_pay', ['return_url', 'cancel_url']],
    'venmo' => ['setPaymentSourceVenmo', ['vault_id' => 'V-1'], 'venmo', ['brand_name', 'order_update_callback_config']],
]);

it('passes the experience context through unfiltered for sources not in the spec', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->setPaymentSourcePayUponInvoice(['email' => 'buyer@example.com'])
        ->setBrandName('Acme')
        ->createOrderWithPaymentSource($this->order);

    expect(sentBody($this->mock)['payment_source']['pay_upon_invoice']['experience_context'])->toBe(['brand_name' => 'Acme']);
});

it('only sends supported application_context fields for subscriptions', function () {
    $this->mock->addResponse(['id' => 'I-1']);

    $this->client->addProductById('PROD-1')
        ->addBillingPlanById('P-1')
        ->setBrandName('Acme')
        ->setReturnAndCancelUrl('https://example.com/ok', 'https://example.com/cancel')
        ->setShippingAddressChangeCallback('https://example.com/cb')
        ->setStoredPaymentSource('CUSTOMER', 'ONE_TIME', 'IMMEDIATE')
        ->setupSubscription('John Doe', 'john@example.com');

    expect(sentBody($this->mock)['application_context'])->toBe([
        'brand_name' => 'Acme',
        'return_url' => 'https://example.com/ok',
        'cancel_url' => 'https://example.com/cancel',
    ]);
});
