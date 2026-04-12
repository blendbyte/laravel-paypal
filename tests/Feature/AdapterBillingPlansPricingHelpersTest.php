<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('processBillingPlanPricingUpdates throws when no billing plan is set', function () {
    $client = $this->createPartialMock(\Srmklive\PayPal\Services\PayPal::class, []);

    expect(fn () => $client->processBillingPlanPricingUpdates())
        ->toThrow(RuntimeException::class, 'No billing plan set');
});

it('can update pricing schemes for a billing plan', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client = $this->client->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
        ->addPricingScheme('DAY', 7, 0, true)
        ->addPricingScheme('MONTH', 1, 100);

    $this->client->setClient(
        $this->mock_http_client(false)
    );

    $response = $this->client->processBillingPlanPricingUpdates();

    expect($response)->toBeEmpty();
});

it('can set custom limits when listing billing plans', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client = $this->client->setPageSize(30)
        ->showTotals(true);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockListPlansResponse()
        )
    );

    $response = $this->client->setCurrentPage(1)->listPlans();

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('plans');
});
