<?php

use Illuminate\Http\Request;
use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
    $this->headers = [
        'auth_algo' => 'SHA256withRSA',
        'cert_url' => 'https://api.paypal.com/v1/notifications/certs/CERT-1',
        'transmission_id' => 'trans-1',
        'transmission_sig' => 'c2ln',
        'transmission_time' => '2026-10-01T12:00:00Z',
        'webhook_id' => 'WH-1',
    ];
    // Slashes, non-ASCII and a float with a zero fraction all change when re-encoded.
    $this->rawEvent = '{"id":"WH-EVT-1","resource":{"href":"https://api.paypal.com/v2/x","note":"Grüße","amount":10.0}}';
});

it('sends a raw webhook_event string byte for byte', function () {
    $this->mock->addResponse(['verification_status' => 'SUCCESS']);

    $response = $this->client->verifyWebHook($this->headers + ['webhook_event' => $this->rawEvent]);

    $body = (string) $this->mock->lastRequest()->getBody();
    expect($response)->toBe(['verification_status' => 'SUCCESS'])
        ->and($body)->toEndWith('"webhook_event":'.$this->rawEvent.'}')
        ->and(json_decode($body, true))->toMatchArray($this->headers)
        ->and($this->mock->lastRequest()->getHeaderLine('Content-Type'))->toBe('application/json');
});

it('still JSON-encodes an array webhook_event', function () {
    $this->mock->addResponse(['verification_status' => 'SUCCESS']);

    $this->client->verifyWebHook($this->headers + ['webhook_event' => ['id' => 'WH-EVT-1']]);

    expect(json_decode((string) $this->mock->lastRequest()->getBody(), true)['webhook_event'])->toBe(['id' => 'WH-EVT-1']);
});

it('rejects a webhook_event string that is not a JSON object without sending a request', function (string $event) {
    expect($this->client->verifyWebHook($this->headers + ['webhook_event' => $event]))
        ->toBe(['error' => 'Invalid webhook_event: expected the raw JSON request body'])
        ->and($this->mock->requests())->toBe([]);
})->with(['not json', '"a string"', '[1,2]']);

it('does not leak the raw body into the next request', function () {
    $this->mock->addResponse(['verification_status' => 'SUCCESS']);
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->verifyWebHook($this->headers + ['webhook_event' => $this->rawEvent]);
    $this->client->showOrderDetails('O-1');

    expect((string) $this->mock->lastRequest()->getBody())->toBe('');
});

it('verifyIPN sends the raw request body as webhook_event', function () {
    $this->mock->addResponse(['verification_status' => 'SUCCESS']);
    $this->client->setWebHookID('WH-1');

    $request = Request::create('/', 'POST', [], [], [], [
        'HTTP_PAYPAL_AUTH_ALGO' => 'SHA256withRSA',
        'HTTP_PAYPAL_TRANSMISSION_ID' => 'trans-1',
        'HTTP_PAYPAL_CERT_URL' => 'https://api.paypal.com/v1/notifications/certs/CERT-1',
        'HTTP_PAYPAL_TRANSMISSION_SIG' => 'c2ln',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '2026-10-01T12:00:00Z',
    ], $this->rawEvent);

    $this->client->verifyIPN($request);

    expect((string) $this->mock->lastRequest()->getBody())->toEndWith('"webhook_event":'.$this->rawEvent.'}');
});

it('throws PayPalApiException for an invalid raw webhook_event in exception mode', function () {
    $this->client->withExceptions();

    expect(fn () => $this->client->verifyWebHook($this->headers + ['webhook_event' => 'not json']))
        ->toThrow(\Srmklive\PayPal\Exceptions\PayPalApiException::class, 'Invalid webhook_event')
        ->and($this->mock->requests())->toBe([]);
});
