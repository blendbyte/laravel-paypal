<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

function simulateBody(MockPayPalClient $mock): array
{
    return json_decode((string) $mock->lastRequest()->getBody(), true);
}

it('simulates a webhook event for a webhook ID', function () {
    $event = ['id' => 'WH-SIM-1', 'event_type' => 'PAYMENT.CAPTURE.COMPLETED'];
    $this->mock->addResponse($event, 202);

    $response = $this->client->simulateWebHookEvent('PAYMENT.CAPTURE.COMPLETED', '8PT597110X687430LKGECATA');

    $request = $this->mock->lastRequest();
    expect($response)->toBe($event)
        ->and($request->getMethod())->toBe('POST')
        ->and((string) $request->getUri())->toEndWith('/v1/notifications/simulate-event')
        ->and(simulateBody($this->mock))->toBe(['webhook_id' => '8PT597110X687430LKGECATA', 'event_type' => 'PAYMENT.CAPTURE.COMPLETED']);
});

it('simulates a webhook event for a URL with a resource version', function () {
    $this->mock->addResponse(['id' => 'WH-SIM-2'], 202);

    $this->client->simulateWebHookEvent('PAYMENT.CAPTURE.DENIED', url: 'https://example.com/hook', resource_version: '2.0');

    expect(simulateBody($this->mock))->toBe([
        'url' => 'https://example.com/hook',
        'event_type' => 'PAYMENT.CAPTURE.DENIED',
        'resource_version' => '2.0',
    ]);
});

it('requires a webhook ID or URL to simulate a webhook event', function () {
    expect(fn () => $this->client->simulateWebHookEvent('PAYMENT.CAPTURE.COMPLETED'))
        ->toThrow(InvalidArgumentException::class, 'webhook ID or a URL')
        ->and($this->mock->requests())->toBe([]);
});
