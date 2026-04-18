<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Testing\MockPayPalClient;
use Srmklive\PayPal\Tests\MockRequestPayloads;
use Srmklive\PayPal\Tests\MockResponsePayloads;

uses(MockRequestPayloads::class, MockResponsePayloads::class);

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('setPartnerAttributionId is fluent', function () {
    $result = $this->client->setPartnerAttributionId('TestPlatform_SP');

    expect($result)->toBeInstanceOf(PayPalClient::class);
});

it('setPartnerAttributionId sets the correct header value', function () {
    $this->client->setPartnerAttributionId('TestPlatform_SP');

    expect($this->client->getRequestHeader('PayPal-Partner-Attribution-Id'))->toBe('TestPlatform_SP');
});

it('partner attribution id persists across multiple requests', function () {
    $mock = new MockPayPalClient();
    $mock->addResponse(['id' => 'ORDER-1', 'status' => 'CREATED']);
    $mock->addResponse(['id' => 'ORDER-2', 'status' => 'CREATED']);

    $provider = $mock->mockProvider();
    $provider->setPartnerAttributionId('MyPlatform_BN');

    $provider->createOrder($this->createOrderParams());
    expect($mock->requests()[0]->getHeaderLine('PayPal-Partner-Attribution-Id'))->toBe('MyPlatform_BN');

    $provider->createOrder($this->createOrderParams());
    expect($mock->requests()[1]->getHeaderLine('PayPal-Partner-Attribution-Id'))->toBe('MyPlatform_BN');
});
