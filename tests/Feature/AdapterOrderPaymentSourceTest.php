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
