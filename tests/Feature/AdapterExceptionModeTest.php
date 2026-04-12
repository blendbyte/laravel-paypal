<?php

use Blendbyte\PayPal\Exceptions\PayPalApiException;
use Blendbyte\PayPal\Services\PayPal as PayPalClient;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

uses(MockRequestPayloads::class);

/**
 * Build a Guzzle mock client that returns the given status code and body.
 */
function mockErrorClient(int $status, string $body): HttpClient
{
    $mock    = new MockHandler([new Response($status, [], $body)]);
    $handler = HandlerStack::create($mock);

    return new HttpClient(['handler' => $handler]);
}

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $this->client->getAccessToken();
});

// ── Default (silent) mode ─────────────────────────────────────────────────

it('returns error array by default on a failed request', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    $response = $this->client->showOrderDetails('bad-id');

    expect($response)->toHaveKey('error');
});

it('does not throw by default on a failed request', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    expect(fn () => $this->client->showOrderDetails('bad-id'))->not->toThrow(PayPalApiException::class);
});

// ── Exception mode ────────────────────────────────────────────────────────

it('withExceptions() is fluent', function () {
    expect($this->client->withExceptions())->toBeInstanceOf(PayPalClient::class);
});

it('throws PayPalApiException on error when withExceptions() is enabled', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    expect(fn () => $this->client->showOrderDetails('bad-id'))->toThrow(PayPalApiException::class);
});

it('getPayPalError() returns the decoded error payload', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        expect($e->getPayPalError())->toBeArray();
        expect($e->getPayPalError())->toHaveKey('name');
    }
});

it('getMessage() contains the error information', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        expect($e->getMessage())->toContain('UNPROCESSABLE_ENTITY');
    }
});

it('getPayPalError() returns a string for non-JSON error bodies', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(503, 'Service Unavailable'));

    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        expect($e->getPayPalError())->toBeString();
        expect($e->getPayPalError())->toBe('Service Unavailable');
    }
});

// ── Toggling back ─────────────────────────────────────────────────────────

it('withoutExceptions() reverts to silent error mode', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions()->withoutExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    $response = $this->client->showOrderDetails('bad-id');

    expect($response)->toHaveKey('error');
});
