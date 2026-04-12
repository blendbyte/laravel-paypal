<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

it('can be instantiated', function () {
    $client = new PayPalClient($this->getMockCredentials());

    expect($client)->toBeInstanceOf(PayPalClient::class);
});

it('throws exception if invalid credentials are provided', function () {
    expect(fn () => new PayPalClient)->toThrow(RuntimeException::class);
});

it('throws exception if invalid mode is provided', function () {
    $credentials = $this->getMockCredentials();
    $credentials['mode'] = '';

    expect(fn () => new PayPalClient($credentials))->toThrow(RuntimeException::class);
});

it('throws exception if empty credentials are provided', function () {
    $credentials = $this->getMockCredentials();
    $credentials['sandbox'] = [];

    expect(fn () => new PayPalClient($credentials))->toThrow(RuntimeException::class);
});

it('throws exception if credentials items are not provided', function () {
    $item = 'client_id';

    $credentials = $this->getMockCredentials();
    $credentials['sandbox'][$item] = '';

    expect(fn () => new PayPalClient($credentials))->toThrow(RuntimeException::class);
});

it('can get access token', function () {
    $expectedResponse = $this->mockAccessTokenResponse();

    $expectedMethod = 'getAccessToken';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, false);

    $mockClient->setApiCredentials($this->getMockCredentials());

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});
