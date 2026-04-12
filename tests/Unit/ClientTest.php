<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

// ---------------------------------------------------------------------------
// getAccessToken() — request construction
//
// These tests verify that makeHttpRequest() correctly translates
// options['auth'] and options['form_params'] into a proper PSR-7 request.
// Before the PSR-18 migration fix, both options were silently dropped,
// meaning every getAccessToken() call sent an unauthenticated empty POST
// to PayPal and received a 401 in return.
// ---------------------------------------------------------------------------

it('getAccessToken sends Basic Auth header with base64-encoded credentials', function () {
    $credentials = $this->getMockCredentials();
    $client = new PayPalClient($credentials);

    $container = [];
    $client->setClient($this->mock_http_client_capturing($this->mockAccessTokenResponse(), $container));

    $client->getAccessToken();

    expect($container)->toHaveCount(1);

    /** @var \Psr\Http\Message\RequestInterface $request */
    $request = $container[0]['request'];

    $expected = 'Basic '.base64_encode(
        $credentials['sandbox']['client_id'].':'.$credentials['sandbox']['client_secret']
    );

    expect($request->getHeaderLine('Authorization'))->toBe($expected);
});

it('getAccessToken sends grant_type=client_credentials as URL-encoded body', function () {
    $credentials = $this->getMockCredentials();
    $client = new PayPalClient($credentials);

    $container = [];
    $client->setClient($this->mock_http_client_capturing($this->mockAccessTokenResponse(), $container));

    $client->getAccessToken();

    /** @var \Psr\Http\Message\RequestInterface $request */
    $request = $container[0]['request'];

    expect($request->getHeaderLine('Content-Type'))->toBe('application/x-www-form-urlencoded');
    expect((string) $request->getBody())->toBe('grant_type=client_credentials');
});

it('getAccessToken posts to the correct OAuth2 token endpoint', function () {
    $credentials = $this->getMockCredentials();
    $client = new PayPalClient($credentials);

    $container = [];
    $client->setClient($this->mock_http_client_capturing($this->mockAccessTokenResponse(), $container));

    $client->getAccessToken();

    /** @var \Psr\Http\Message\RequestInterface $request */
    $request = $container[0]['request'];

    expect(strtoupper($request->getMethod()))->toBe('POST');
    expect((string) $request->getUri())->toBe('https://api-m.sandbox.paypal.com/v1/oauth2/token');
});

it('getAccessToken does not leak auth options into subsequent requests', function () {
    $credentials = $this->getMockCredentials();
    $client = new PayPalClient($credentials);

    // Two queued responses: one for getAccessToken, one for the next call.
    $container = [];
    $client->setClient($this->mock_http_client_capturing($this->mockAccessTokenResponse(), $container));
    $client->getAccessToken();

    // Queue a second call (e.g. showOrderDetails) and capture its request.
    $container2 = [];
    $client->setClient($this->mock_http_client_capturing(['id' => 'ORDER-1'], $container2));
    $client->showOrderDetails('ORDER-1');

    /** @var \Psr\Http\Message\RequestInterface $followUp */
    $followUp = $container2[0]['request'];

    // Must use Bearer, not Basic, for subsequent calls.
    expect($followUp->getHeaderLine('Authorization'))->toStartWith('Bearer ');
    // form_params body must not leak through.
    expect((string) $followUp->getBody())->toBe('');
});

// ---------------------------------------------------------------------------
// JSON request construction — sanity-check the non-auth path
// ---------------------------------------------------------------------------

it('regular API calls send JSON body with correct Content-Type', function () {
    $credentials = $this->getMockCredentials();
    $client = new PayPalClient($credentials);

    $client->setAccessToken(['access_token' => 'test-token', 'token_type' => 'Bearer']);

    $container = [];
    $client->setClient($this->mock_http_client_capturing(['id' => 'ORDER-123'], $container));

    $orderData = ['intent' => 'CAPTURE', 'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]]];
    $client->createOrder($orderData);

    /** @var \Psr\Http\Message\RequestInterface $request */
    $request = $container[0]['request'];

    expect($request->getHeaderLine('Content-Type'))->toBe('application/json');
    expect(json_decode((string) $request->getBody(), true))->toBe($orderData);
    expect($request->getHeaderLine('Authorization'))->toBe('Bearer test-token');
});
