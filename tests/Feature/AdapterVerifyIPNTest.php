<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $this->client->getAccessToken();
});

it('returns error when PayPal webhook headers are missing', function () {
    // Regression: \Log::error() was called without the facade being imported.
    // In a real Laravel app the global alias works, but in tests (or apps that
    // do not load the alias loader) it throws "Class Log not found".
    // The log call has been removed — errors are communicated via the return value.

    $request = Request::create('/', 'POST');

    $this->client->setWebHookID('test-webhook-id');
    $response = $this->client->verifyIPN($request);

    expect($response)->toBe(['error' => 'Invalid headers or webhook id provided']);
});

it('returns error when request body is empty', function () {
    // Regression: json_decode() returns null on empty/invalid body, which was
    // forwarded verbatim as webhook_event — PayPal rejects {"webhook_event":null}.
    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL-AUTH-ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL-TRANSMISSION-ID'   => 'abc123',
        'HTTP_PAYPAL-CERT-URL'          => 'https://api.paypal.com/cert.pem',
        'HTTP_PAYPAL-TRANSMISSION-SIG'  => base64_encode('sig'),
        'HTTP_PAYPAL-TRANSMISSION-TIME' => '2016-02-18T20:01:35Z',
    ], '');

    $this->client->setWebHookID('test-webhook-id');
    $response = $this->client->verifyIPN($request);

    expect($response)->toBe(['error' => 'Invalid or empty request body']);
});

it('returns error when request body is not valid JSON', function () {
    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL-AUTH-ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL-TRANSMISSION-ID'   => 'abc123',
        'HTTP_PAYPAL-CERT-URL'          => 'https://api.paypal.com/cert.pem',
        'HTTP_PAYPAL-TRANSMISSION-SIG'  => base64_encode('sig'),
        'HTTP_PAYPAL-TRANSMISSION-TIME' => '2016-02-18T20:01:35Z',
    ], 'not-valid-json{{');

    $this->client->setWebHookID('test-webhook-id');
    $response = $this->client->verifyIPN($request);

    expect($response)->toBe(['error' => 'Invalid or empty request body']);
});

it('returns error when webhook_id has not been set', function () {
    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL-AUTH-ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL-TRANSMISSION-ID'   => 'abc123',
        'HTTP_PAYPAL-CERT-URL'          => 'https://api.paypal.com/cert.pem',
        'HTTP_PAYPAL-TRANSMISSION-SIG'  => base64_encode('sig'),
        'HTTP_PAYPAL-TRANSMISSION-TIME' => '2016-02-18T20:01:35Z',
    ]);

    // setWebHookID() is deliberately not called — $this->webhook_id stays null.
    $response = $this->client->verifyIPN($request);

    expect($response)->toBe(['error' => 'Invalid headers or webhook id provided']);
});
