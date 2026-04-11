<?php

use Blendbyte\PayPal\Services\PayPal as PayPalClient;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Carbon\Carbon;

uses(MockRequestPayloads::class);

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('can set shipping address change callback url', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setShippingAddressChangeCallback('https://example.com/shipping-callback');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockConfirmOrderResponse()
        )
    );

    $response = $this->client->setupOrderConfirmation('5O190127TN364715T', 'ORDER_COMPLETE_ON_PAYMENT_APPROVAL');

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});

it('can confirm payment for an order', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->subDays(10)->toDateString();

    $this->client = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setBrandName('Test Brand')
        ->setStoredPaymentSource(
            'MERCHANT',
            'RECURRING',
            'RESUBMISSION',
            true,
            '5TY05013RG002845M',
            $start_date,
            'Invoice-005',
            'VISA'
        );

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockConfirmOrderResponse()
        )
    );

    $response = $this->client->setupOrderConfirmation('5O190127TN364715T', 'ORDER_COMPLETE_ON_PAYMENT_APPROVAL');

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
});
