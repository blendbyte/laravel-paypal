<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create a payment source token', function () {
    $expectedResponse = $this->mockCreatePaymentMethodsTokenResponse();
    $expectedParams   = $this->mockCreatePaymentSetupTokensParams();
    $expectedMethod   = 'createPaymentSourceToken';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can list payment source tokens', function () {
    $expectedResponse = $this->mockListPaymentMethodsTokensResponse();
    $expectedMethod   = 'listPaymentSourceTokens';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(1, 10, true))->toBe($expectedResponse);
});

it('can show payment source token details', function () {
    $expectedResponse = $this->mockCreatePaymentMethodsTokenResponse();
    $expectedMethod   = 'showPaymentSourceTokenDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('8kk8451t'))->toBe($expectedResponse);
});

it('can delete a payment source token', function () {
    $expectedResponse = '';
    $expectedMethod   = 'deletePaymentSourceToken';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('8kk8451t'))->toBe($expectedResponse);
});

it('can create a payment setup token', function () {
    $expectedResponse = $this->mockCreatePaymentSetupTokenResponse();
    $expectedParams   = $this->mockCreatePaymentSetupPayPalParams();
    $expectedMethod   = 'createPaymentSetupToken';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can show payment setup token details', function () {
    $expectedResponse = $this->mockListPaymentSetupTokenResponse();
    $expectedMethod   = 'showPaymentSetupTokenDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('5C991763VB2781612'))->toBe($expectedResponse);
});

it('can delete a payment setup token', function () {
    $expectedResponse = '';
    $expectedMethod   = 'deletePaymentSetupToken';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('5C991763VB2781612'))->toBe($expectedResponse);
});
