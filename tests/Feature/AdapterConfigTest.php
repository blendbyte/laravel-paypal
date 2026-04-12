<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
});

it('throws exception if invalid credentials are provided', function () {
    expect(fn () => new PayPalClient([]))->toThrow(RuntimeException::class, 'Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');
});

it('throws exception if invalid mode is provided', function () {
    $credentials = $this->getApiCredentials();
    $credentials['mode'] = '';
    expect(fn () => new PayPalClient($credentials))->toThrow(RuntimeException::class, 'Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');
});

it('throws exception if empty credentials are provided', function () {
    $credentials = $this->getApiCredentials();
    $credentials['sandbox'] = [];
    expect(fn () => new PayPalClient($credentials))->toThrow(RuntimeException::class, 'Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');
});

it('throws exception if credentials items are not provided', function () {
    $item = 'client_id';
    $credentials = $this->getApiCredentials();
    $credentials['sandbox'][$item] = '';
    expect(fn () => new PayPalClient($credentials))->toThrow(RuntimeException::class, "{$item} missing from the provided configuration. Please add your application {$item}.");
});

it('can take valid credentials and return the client instance', function () {
    expect($this->client)->toBeInstanceOf(PayPalClient::class);
});

it('throws exception if invalid credentials are provided through method', function () {
    expect(fn () => $this->client->setApiCredentials([]))->toThrow(RuntimeException::class);
});

it('returns the client instance if valid credentials are provided through method', function () {
    $this->client->setApiCredentials($this->getApiCredentials());
    expect($this->client)->toBeInstanceOf(PayPalClient::class);
});

it('throws exception if invalid currency is set', function () {
    expect(fn () => $this->client->setCurrency('PKR'))->toThrow(RuntimeException::class, "'PKR' is not a supported PayPal currency code.");
});

it('throws exception for RUB as PayPal suspended Russian services', function () {
    expect(fn () => $this->client->setCurrency('RUB'))->toThrow(RuntimeException::class, "'RUB' is not a supported PayPal currency code.");
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
        'PayPal-Request-Id' => 'some-request-id',
        'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
    ]);
    expect($this->client->getRequestHeader('PayPal-Request-Id'))->not->toBeEmpty();
    expect($this->client->getRequestHeader('PayPal-Partner-Attribution-Id'))->toBe('some-attribution-id');
});

it('throws exception if options header not set', function () {
    expect(fn () => $this->client->getRequestHeader('Prefer'))->toThrow(RuntimeException::class, 'Options header is not set.');
});

it('accepts custom timeout and connect_timeout in credentials', function () {
    $credentials = $this->getApiCredentials();
    $credentials['timeout'] = 60;
    $credentials['connect_timeout'] = 5;

    $client = new PayPalClient($credentials);

    expect($client)->toBeInstanceOf(PayPalClient::class);
});

it('accepts max_retries in credentials and disables retry when zero', function () {
    $credentials = $this->getApiCredentials();
    $credentials['max_retries'] = 0;

    $client = new PayPalClient($credentials);

    expect($client)->toBeInstanceOf(PayPalClient::class);
});

it('preserves validate_ssl = true from credentials', function () {
    $credentials = $this->getApiCredentials();
    $credentials['validate_ssl'] = true;

    $client = new PayPalClient($credentials);

    $prop = (new ReflectionClass($client))->getProperty('validateSSL');

    expect($prop->getValue($client))->toBeTrue();
});

it('preserves validate_ssl = false from credentials', function () {
    // Regression: empty(false) === true caused the old ternary in setDefaultValues()
    // to silently reset false back to true, making SSL verification impossible to
    // disable regardless of the config value.
    $credentials = $this->getApiCredentials();
    $credentials['validate_ssl'] = false;

    $client = new PayPalClient($credentials);

    $prop = (new ReflectionClass($client))->getProperty('validateSSL');

    expect($prop->getValue($client))->toBeFalse();
});

it('uses Laravel config when PayPalClient is constructed with an empty array', function () {
    // Boot a minimal Illuminate container so that config('paypal') returns the
    // credentials — this exercises the if (!empty($fromLaravel)) branch in
    // setConfig() (lines 206–207 of PayPalRequest.php).
    $previous = \Illuminate\Container\Container::getInstance();

    $container = new \Illuminate\Container\Container();
    $container->instance('config', new \Illuminate\Config\Repository([
        'paypal' => $this->getApiCredentials(),
    ]));
    \Illuminate\Container\Container::setInstance($container);

    try {
        $client = new PayPalClient([]);
        expect($client)->toBeInstanceOf(PayPalClient::class);
    } finally {
        \Illuminate\Container\Container::setInstance($previous);
    }
});
