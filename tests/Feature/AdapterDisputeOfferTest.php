<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
    $this->mock->addResponse(['links' => []]);
});

function offerBody(MockPayPalClient $mock): array
{
    return json_decode((string) $mock->lastRequest()->getBody(), true);
}

it('sends the offer amount for refund offers', function () {
    $this->client->makeOfferToResolveDispute('PP-D-1', 'Offer', 25, 'REFUND');

    expect(offerBody($this->mock))->toBe([
        'note' => 'Offer',
        'offer_type' => 'REFUND',
        'offer_amount' => ['currency_code' => 'USD', 'value' => '25.00'],
    ]);
});

it('omits the offer amount for replacement-only offers', function () {
    $this->client->makeOfferToResolveDispute('PP-D-1', 'Replacement', 0, 'REPLACEMENT_WITHOUT_REFUND');

    expect(offerBody($this->mock))->toBe(['note' => 'Replacement', 'offer_type' => 'REPLACEMENT_WITHOUT_REFUND']);
});

it('sends the return shipping address and invoice id from $data', function () {
    $address = ['address_line_1' => '1 Main St', 'admin_area_2' => 'San Jose', 'postal_code' => '95131', 'country_code' => 'US'];

    $this->client->makeOfferToResolveDispute('PP-D-1', 'Return it', 25, 'REFUND_WITH_RETURN', [
        'return_shipping_address' => $address,
        'invoice_id' => 'INV-1',
    ]);

    expect(offerBody($this->mock))->toBe([
        'return_shipping_address' => $address,
        'invoice_id' => 'INV-1',
        'note' => 'Return it',
        'offer_type' => 'REFUND_WITH_RETURN',
        'offer_amount' => ['currency_code' => 'USD', 'value' => '25.00'],
    ]);
});
