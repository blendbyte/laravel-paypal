<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

// ---------------------------------------------------------------------------
// setWebHookID — state setter
// ---------------------------------------------------------------------------

it('setWebHookID sets webhook_id state and returns $this', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $result = $client->setWebHookID('WH-ABCD-1234');

    expect($result)->toBeInstanceOf(PayPalClient::class);
    $id = (new ReflectionProperty(PayPalClient::class, 'webhook_id'))->getValue($client);
    expect($id)->toBe('WH-ABCD-1234');
});

// ---------------------------------------------------------------------------
// verifyIPN — header validation
// ---------------------------------------------------------------------------

it('returns error when all PayPal headers are missing', function () {
    $client  = $this->createPartialMock(PayPalClient::class, []);
    $request = Request::create('/', 'POST', [], [], [], [], '{}');

    expect($client->verifyIPN($request))->toBe(['error' => 'Invalid headers or webhook id provided']);
});

it('returns error when PAYPAL-AUTH-ALGO header is missing', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->setWebHookID('WH-TEST');

    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL_TRANSMISSION_ID'   => 'trans-123',
        'HTTP_PAYPAL_CERT_URL'          => 'https://api.paypal.com/cert',
        'HTTP_PAYPAL_TRANSMISSION_SIG'  => 'sig-abc',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '2024-01-01T00:00:00Z',
        // HTTP_PAYPAL_AUTH_ALGO intentionally omitted
    ], '{}');

    expect($client->verifyIPN($request))->toBe(['error' => 'Invalid headers or webhook id provided']);
});

it('returns error when PAYPAL-TRANSMISSION-ID header is missing', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->setWebHookID('WH-TEST');

    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL_AUTH_ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL_CERT_URL'          => 'https://api.paypal.com/cert',
        'HTTP_PAYPAL_TRANSMISSION_SIG'  => 'sig-abc',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '2024-01-01T00:00:00Z',
    ], '{}');

    expect($client->verifyIPN($request))->toBe(['error' => 'Invalid headers or webhook id provided']);
});

it('returns error when webhook_id has not been set', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    // webhook_id NOT set via setWebHookID()

    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL_AUTH_ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL_TRANSMISSION_ID'   => 'trans-123',
        'HTTP_PAYPAL_CERT_URL'          => 'https://api.paypal.com/cert',
        'HTTP_PAYPAL_TRANSMISSION_SIG'  => 'sig-abc',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '2024-01-01T00:00:00Z',
    ], '{}');

    expect($client->verifyIPN($request))->toBe(['error' => 'Invalid headers or webhook id provided']);
});

// ---------------------------------------------------------------------------
// verifyIPN — body validation
// ---------------------------------------------------------------------------

it('returns error when request body is invalid JSON', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->setWebHookID('WH-TEST');

    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL_AUTH_ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL_TRANSMISSION_ID'   => 'trans-123',
        'HTTP_PAYPAL_CERT_URL'          => 'https://api.paypal.com/cert',
        'HTTP_PAYPAL_TRANSMISSION_SIG'  => 'sig-abc',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '2024-01-01T00:00:00Z',
    ], 'not-valid-json');

    expect($client->verifyIPN($request))->toBe(['error' => 'Invalid or empty request body']);
});

it('returns error when request body is empty', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $client->setWebHookID('WH-TEST');

    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL_AUTH_ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL_TRANSMISSION_ID'   => 'trans-123',
        'HTTP_PAYPAL_CERT_URL'          => 'https://api.paypal.com/cert',
        'HTTP_PAYPAL_TRANSMISSION_SIG'  => 'sig-abc',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '2024-01-01T00:00:00Z',
    ], '');

    expect($client->verifyIPN($request))->toBe(['error' => 'Invalid or empty request body']);
});

// ---------------------------------------------------------------------------
// verifyIPN — header case-insensitivity and delegation
// ---------------------------------------------------------------------------

it('handles headers regardless of original case via array_change_key_case', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['verifyWebHook']);
    $client->setWebHookID('WH-TEST');

    $client->method('verifyWebHook')->willReturn(['verification_status' => 'SUCCESS']);

    // Laravel normalises all header names to lowercase internally; after
    // array_change_key_case(..., CASE_UPPER) in verifyIPN() they become uppercase.
    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL_AUTH_ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL_TRANSMISSION_ID'   => 'trans-123',
        'HTTP_PAYPAL_CERT_URL'          => 'https://api.paypal.com/cert',
        'HTTP_PAYPAL_TRANSMISSION_SIG'  => 'c2lnLWFiYw==',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '2024-01-01T00:00:00Z',
    ], '{"event_type":"PAYMENT.SALE.COMPLETED"}');

    $result = $client->verifyIPN($request);

    expect($result)->toBe(['verification_status' => 'SUCCESS']);
});

it('delegates to verifyWebHook with the correct payload structure', function () {
    $capturedPayload = null;

    $client = $this->createPartialMock(PayPalClient::class, ['verifyWebHook']);
    $client->setWebHookID('WH-CAPTURE-TEST');

    $client->expects($this->once())
        ->method('verifyWebHook')
        ->willReturnCallback(function (array $payload) use (&$capturedPayload) {
            $capturedPayload = $payload;

            return ['verification_status' => 'SUCCESS'];
        });

    $body    = '{"event_type":"PAYMENT.CAPTURE.COMPLETED","id":"WH-EVT-001"}';
    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL_AUTH_ALGO'         => 'SHA256withRSA',
        'HTTP_PAYPAL_TRANSMISSION_ID'   => 'trans-456',
        'HTTP_PAYPAL_CERT_URL'          => 'https://api.paypal.com/cert',
        'HTTP_PAYPAL_TRANSMISSION_SIG'  => 'c2lnLWFiYw==',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '2024-06-01T12:00:00Z',
    ], $body);

    $client->verifyIPN($request);

    expect($capturedPayload['auth_algo'])->toBe('SHA256withRSA');
    expect($capturedPayload['transmission_id'])->toBe('trans-456');
    expect($capturedPayload['cert_url'])->toBe('https://api.paypal.com/cert');
    expect($capturedPayload['webhook_id'])->toBe('WH-CAPTURE-TEST');
    expect($capturedPayload['webhook_event'])->toBeObject();
    expect($capturedPayload['webhook_event']->event_type)->toBe('PAYMENT.CAPTURE.COMPLETED');
});
