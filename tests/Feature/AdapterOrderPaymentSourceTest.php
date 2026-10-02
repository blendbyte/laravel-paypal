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
