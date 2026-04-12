<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('can create payment token from a vault token', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
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
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
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
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
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
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
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

it('can delete a payment setup token', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client(false));

    $response = $this->client->deletePaymentSetupToken('8XF08998X3492364U');

    expect($response)->toBeEmpty();
});

it('can set customer id for vault operations', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePaymentMethodsTokenResponse()
        )
    );

    $this->client = $this->client->setTokenSource('5C991763VB2781612', 'SETUP_TOKEN')
        ->setCustomerId('customer_4029352050');

    $response = $this->client->sendPaymentMethodRequest();

    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('customer');
});

it('can set payment source for apple pay', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $response_data = $this->mockCreatePaymentSetupTokenResponse();
    $response_data['payment_source']['apple_pay'] = ['token' => ['id' => 'abc123', 'type' => 'APPLE_PAY']];
    unset($response_data['payment_source']['card']);

    $this->client->setClient($this->mock_http_client($response_data));

    $this->client = $this->client->setPaymentSourceApplePay(['token' => ['id' => 'abc123', 'type' => 'APPLE_PAY']])
        ->setCustomerSource('customer_4029352050');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});

it('can set payment source for google pay', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $response_data = $this->mockCreatePaymentSetupTokenResponse();
    $response_data['payment_source']['google_pay'] = ['card' => ['name' => 'John Doe']];
    unset($response_data['payment_source']['card']);

    $this->client->setClient($this->mock_http_client($response_data));

    $this->client = $this->client->setPaymentSourceGooglePay(['card' => ['name' => 'John Doe']])
        ->setCustomerSource('customer_4029352050');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});

it('can set card billing address for acdc', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreatePaymentSetupTokenResponse()));

    $this->client = $this->client
        ->setPaymentSourceCard(['name' => 'John Doe'])
        ->setCardBillingAddress('123 Main St', 'San Jose', 'CA', '95131', 'US');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});

it('can set card vaulting for acdc', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreatePaymentSetupTokenResponse()));

    $this->client = $this->client
        ->setPaymentSourceCard(['name' => 'John Doe'])
        ->setCardVaulting('ON_SUCCESS');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});

it('can set card verification method for acdc', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreatePaymentSetupTokenResponse()));

    $this->client = $this->client
        ->setPaymentSourceCard(['name' => 'John Doe'])
        ->setCardVerification('SCA_ALWAYS');

    $response = $this->client->sendPaymentMethodRequest(true);

    expect($response)->toHaveKey('payment_source');
});

it('can combine card vaulting and verification without overwriting each other', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient($this->mock_http_client($this->mockCreatePaymentSetupTokenResponse()));

    $this->client = $this->client
        ->setPaymentSourceCard(['name' => 'John Doe'])
        ->setCardVaulting('ON_SUCCESS')
        ->setCardVerification('SCA_WHEN_REQUIRED');

    // Verify both attributes are set correctly on the payment source
    $source = $this->client->getPaymentSource();
    expect($source['card']['attributes'])->toHaveKey('vault');
    expect($source['card']['attributes'])->toHaveKey('verification');
    expect($source['card']['attributes']['vault']['store_in_vault'])->toBe('ON_SUCCESS');
    expect($source['card']['attributes']['verification']['method'])->toBe('SCA_WHEN_REQUIRED');
});

it('can create payment source from a venmo account', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
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
