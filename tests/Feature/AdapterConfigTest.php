<?php

use Blendbyte\PayPal\Services\PayPal as PayPalClient;

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
});

it('throws exception if invalid credentials are provided', function () {
    expect(fn() => new PayPalClient([]))->toThrow(\RuntimeException::class, 'Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');
});

it('throws exception if invalid mode is provided', function () {
    $credentials = $this->getApiCredentials();
    $credentials['mode'] = '';
    expect(fn() => new PayPalClient($credentials))->toThrow(\RuntimeException::class, 'Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');
});

it('throws exception if empty credentials are provided', function () {
    $credentials = $this->getApiCredentials();
    $credentials['sandbox'] = [];
    expect(fn() => new PayPalClient($credentials))->toThrow(\RuntimeException::class, 'Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');
});

it('throws exception if credentials items are not provided', function () {
    $item = 'client_id';
    $credentials = $this->getApiCredentials();
    $credentials['sandbox'][$item] = '';
    expect(fn() => new PayPalClient($credentials))->toThrow(\RuntimeException::class, "{$item} missing from the provided configuration. Please add your application {$item}.");
});

it('can take valid credentials and return the client instance', function () {
    expect($this->client)->toBeInstanceOf(PayPalClient::class);
});

it('throws exception if invalid credentials are provided through method', function () {
    expect(fn() => $this->client->setApiCredentials([]))->toThrow(\RuntimeException::class);
});

it('returns the client instance if valid credentials are provided through method', function () {
    $this->client->setApiCredentials($this->getApiCredentials());
    expect($this->client)->toBeInstanceOf(PayPalClient::class);
});

it('throws exception if invalid currency is set', function () {
    expect(fn() => $this->client->setCurrency('PKR'))->toThrow(\RuntimeException::class);
});

it('can set a valid currency', function () {
    $this->client->setCurrency('EUR');
    expect($this->client->getCurrency())->not->toBeEmpty();
    expect($this->client->getCurrency())->toBe('EUR');
});

it('can set a request header', function () {
    $this->client->setRequestHeader('Prefer', 'return=representation');
    expect($this->client->getRequestHeader('Prefer'))->not->toBeEmpty();
    expect($this->client->getRequestHeader('Prefer'))->toBe('return=representation');
});

it('can set multiple request headers', function () {
    $this->client->setRequestHeaders([
        'PayPal-Request-Id'             => 'some-request-id',
        'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
    ]);
    expect($this->client->getRequestHeader('PayPal-Request-Id'))->not->toBeEmpty();
    expect($this->client->getRequestHeader('PayPal-Partner-Attribution-Id'))->toBe('some-attribution-id');
});

it('throws exception if options header not set', function () {
    expect(fn() => $this->client->getRequestHeader('Prefer'))->toThrow(\RuntimeException::class, 'Options header is not set.');
});
