<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

it('sends taxes as plan override when creating a subscription', function () {
    $this->mock->addResponse(['id' => 'I-1']);

    $this->client->addProductById('PROD-1')
        ->addBillingPlanById('P-1')
        ->addTaxes(10)
        ->setupSubscription('John Doe', 'john@example.com');

    $body = json_decode((string) $this->mock->lastRequest()->getBody(), true);

    expect($body)->not->toHaveKey('taxes')
        ->and($body['plan']['taxes'])->toBe(['percentage' => '10.00', 'inclusive' => false]);
});

it('sends taxes and setup fee together in the plan override', function () {
    $this->mock->addResponse(['id' => 'I-1']);

    $this->client->addProductById('PROD-1')
        ->addBillingPlanById('P-1')
        ->addSetupFee(5)
        ->addTaxes(7.5, true)
        ->setupSubscription('John Doe', 'john@example.com');

    $body = json_decode((string) $this->mock->lastRequest()->getBody(), true);

    expect($body['plan']['taxes'])->toBe(['percentage' => '7.50', 'inclusive' => true])
        ->and($body['plan']['payment_preferences']['setup_fee'])->toBe(['value' => '5.00', 'currency_code' => 'USD']);
});

it('omits the plan override when neither taxes nor setup fee are set', function () {
    $this->mock->addResponse(['id' => 'I-1']);

    $this->client->addProductById('PROD-1')
        ->addBillingPlanById('P-1')
        ->setupSubscription('John Doe', 'john@example.com');

    $body = json_decode((string) $this->mock->lastRequest()->getBody(), true);

    expect($body)->not->toHaveKey('plan')
        ->and($body)->not->toHaveKey('taxes');
});
