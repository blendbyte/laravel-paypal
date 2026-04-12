<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Tests\MockRequestPayloads;
use Carbon\Carbon;

uses(MockRequestPayloads::class);

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('can set payment experience context before performing api call', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setBrandName('Test Brand')
        ->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});
