<?php

use Blendbyte\PayPal\Services\PayPal as PayPalClient;
use Blendbyte\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('can create payment token from a vault token', function () {
    $this->client->setAccessToken([
        'access_token'  => $this->access_token,
        'token_type'    => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePaymentMethodsTokenResponse()
        )
    );

    $this->client = $this->client->setTokenSource('5C991763VB2781612', 'SETUP_TOKEN')
    ->setCustomerSource('customer_4029352050');

    $response = $this->client->sendPaymentMethodRequest();

    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('customer');
});

it('can create payment source from a vault token', function () {
    $this->client->setAccessToken([
        'access_token'  => $this->access_token,
        'token_type'    => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePaymentSetupTokenResponse()
        )
    );

    $this->client = $this->client->setTokenSource('5C991763VB2781612', 'SETUP_TOKEN')
    ->setCustomerSource('customer_4029352050');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});

it('can create payment source from a credit card', function () {
    $this->client->setAccessToken([
        'access_token'  => $this->access_token,
        'token_type'    => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePaymentSetupTokenResponse()
        )
    );

    $this->client = $this->client->setPaymentSourceCard($this->mockCreatePaymentSetupTokensParams()['payment_source']['card'])
    ->setCustomerSource('customer_4029352050');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});

it('can create payment source from a paypal account', function () {
    $this->client->setAccessToken([
        'access_token'  => $this->access_token,
        'token_type'    => 'Bearer',
    ]);

    $response_data = $this->mockCreatePaymentSetupTokenResponse();
    $response_data['payment_source']['paypal'] = $this->mockCreatePaymentSetupPayPalParams()['payment_source']['paypal'];
    unset($response_data['payment_source']['card']);

    $this->client->setClient(
        $this->mock_http_client($response_data)
    );

    $this->client = $this->client->setPaymentSourcePayPal($this->mockCreatePaymentSetupPayPalParams()['payment_source']['paypal'])
    ->setCustomerSource('customer_4029352050');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});

it('can create payment source from a venmo account', function () {
    $this->client->setAccessToken([
        'access_token'  => $this->access_token,
        'token_type'    => 'Bearer',
    ]);

    $response_data = $this->mockCreatePaymentSetupTokenResponse();
    $response_data['payment_source']['venmo'] = $this->mockCreatePaymentSetupPayPalParams()['payment_source']['paypal'];
    unset($response_data['payment_source']['card']);

    $this->client->setClient(
        $this->mock_http_client($response_data)
    );

    $this->client = $this->client->setPaymentSourceVenmo($this->mockCreatePaymentSetupPayPalParams()['payment_source']['paypal'])
    ->setCustomerSource('customer_4029352050');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});
