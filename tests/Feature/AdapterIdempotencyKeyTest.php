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

it('withIdempotencyKey is fluent', function () {
    $result = $this->client->withIdempotencyKey('my-key-123');

    expect($result)->toBeInstanceOf(PayPalClient::class);
});

it('withIdempotencyKey sets a specific PayPal-Request-Id header', function () {
    $this->client->withIdempotencyKey('my-key-123');

    expect($this->client->getRequestHeader('PayPal-Request-Id'))->toBe('my-key-123');
});

it('withIdempotencyKey auto-generates a uuid v4 when called without argument', function () {
    $this->client->withIdempotencyKey();

    $key = $this->client->getRequestHeader('PayPal-Request-Id');

    expect($key)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/');
});

it('idempotency key is consumed after one request', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->withIdempotencyKey('single-use-key');
    $this->client->setClient($this->mock_http_client($this->mockOrderDetailsResponse()));
    $this->client->showOrderDetails('5O190127TN364715T');

    expect(fn () => $this->client->getRequestHeader('PayPal-Request-Id'))
        ->toThrow(RuntimeException::class);
});
