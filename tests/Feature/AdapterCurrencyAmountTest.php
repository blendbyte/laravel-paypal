<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
    $this->client->setCurrency('JPY');
});

it('sends zero-decimal amounts for JPY refunds', function () {
    $this->mock->addResponse(['id' => 'R-1']);
    $this->client->refundCapturedPayment('C-1', 'INV-1', 1000, 'Refund');

    $body = json_decode((string) $this->mock->lastRequest()->getBody(), true);

    expect($body['amount'])->toBe(['value' => '1000', 'currency_code' => 'JPY']);
});

it('sends zero-decimal prices for JPY subscription plans and setup fees', function () {
    $this->mock->addResponse(['id' => 'PROD-1']);
    $this->mock->addResponse(['id' => 'P-1']);
    $this->mock->addResponse(['id' => 'I-1']);

    $this->client->addProduct('Yen Product', 'Description', 'SERVICE', 'SOFTWARE')
        ->addMonthlyPlan('Yen Plan', 'Monthly plan', 1000)
        ->addSetupFee(500)
        ->setupSubscription('John Doe', 'john@example.com');

    [, $planRequest, $subscriptionRequest] = $this->mock->requests();
    $plan = json_decode((string) $planRequest->getBody(), true);
    $subscription = json_decode((string) $subscriptionRequest->getBody(), true);

    expect($plan['billing_cycles'][0]['pricing_scheme']['fixed_price'])->toBe(['value' => '1000', 'currency_code' => 'JPY'])
        ->and($subscription['plan']['payment_preferences']['setup_fee'])->toBe(['value' => '500', 'currency_code' => 'JPY']);
});
