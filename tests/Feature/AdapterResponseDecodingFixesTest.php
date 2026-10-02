<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

it('decodes the transaction returned when capturing a subscription payment', function () {
    $transaction = ['id' => 'TX-1', 'status' => 'COMPLETED', 'amount_with_breakdown' => ['gross_amount' => ['currency_code' => 'USD', 'value' => '100.00']]];
    $this->mock->addResponse($transaction);

    expect($this->client->captureSubscriptionPayment('I-1', 'Outstanding balance', 100))->toBe($transaction);
});

it('returns an empty array when a subscription capture is accepted without a body', function () {
    $this->mock->addResponse(false, 202);

    expect($this->client->captureSubscriptionPayment('I-1', 'Outstanding balance', 100))->toBe([]);
});

it('decodes the subsequent action returned when updating a dispute', function () {
    $action = ['links' => [['href' => 'https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-D-1', 'rel' => 'self', 'method' => 'GET']]];
    $this->mock->addResponse($action, 202);

    expect($this->client->updateDispute('PP-D-1', [['op' => 'add', 'path' => '/partner_actions/-', 'value' => []]]))->toBe($action);
});

it('returns an empty array when a dispute update has no body', function () {
    $this->mock->addResponse(false, 204);

    expect($this->client->updateDispute('PP-D-1', []))->toBe([]);
});
