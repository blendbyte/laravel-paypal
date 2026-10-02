<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

function lastBody(MockPayPalClient $mock, int $index = -1): array
{
    $requests = $mock->requests();

    return json_decode((string) $requests[$index < 0 ? count($requests) + $index : $index]->getBody(), true);
}

it('omits empty invoice id and note when capturing an authorization', function () {
    $this->mock->addResponse(['id' => 'C-1']);

    $this->client->captureAuthorizedPayment('A-1', '', 10, '');

    expect(lastBody($this->mock))->toBe([
        'amount' => ['value' => '10.00', 'currency_code' => 'USD'],
        'final_capture' => true,
    ]);
});

it('sends invoice id, note and final_capture false when given', function () {
    $this->mock->addResponse(['id' => 'C-1']);

    $this->client->captureAuthorizedPayment('A-1', 'INV-1', 10, 'Partial capture', false);

    expect(lastBody($this->mock))->toBe([
        'amount' => ['value' => '10.00', 'currency_code' => 'USD'],
        'invoice_id' => 'INV-1',
        'note_to_payer' => 'Partial capture',
        'final_capture' => false,
    ]);
});

it('omits empty invoice id and note when refunding a capture', function () {
    $this->mock->addResponse(['id' => 'R-1']);

    $this->client->refundCapturedPayment('C-1', '', 5, '');

    expect(lastBody($this->mock))->toBe(['amount' => ['value' => '5.00', 'currency_code' => 'USD']]);
});

it('sends invoice id and note when refunding a capture', function () {
    $this->mock->addResponse(['id' => 'R-1']);

    $this->client->refundCapturedPayment('C-1', 'INV-1', 5, 'Damaged item');

    expect(lastBody($this->mock))->toMatchArray(['invoice_id' => 'INV-1', 'note_to_payer' => 'Damaged item']);
});

it('omits empty product and plan descriptions in the subscription helpers', function () {
    $this->mock->addResponse(['id' => 'PROD-1']);
    $this->mock->addResponse(['id' => 'P-1']);

    $this->client->addProduct('Product', '', 'SERVICE', 'SOFTWARE')->addMonthlyPlan('Plan', '', 10);

    expect(lastBody($this->mock, 0))->not->toHaveKey('description')
        ->and(lastBody($this->mock, 1))->not->toHaveKey('description');
});

it('sends product and plan descriptions when given', function () {
    $this->mock->addResponse(['id' => 'PROD-1']);
    $this->mock->addResponse(['id' => 'P-1']);

    $this->client->addProduct('Product', 'A product', 'SERVICE', 'SOFTWARE')->addMonthlyPlan('Plan', 'A plan', 10);

    expect(lastBody($this->mock, 0)['description'])->toBe('A product')
        ->and(lastBody($this->mock, 1)['description'])->toBe('A plan');
});
