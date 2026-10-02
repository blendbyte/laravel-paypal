<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

it('updates order tracking with a JSON patch', function () {
    $this->mock->addResponse(false, 204);

    $patch = [['op' => 'replace', 'path' => '/notify_payer', 'value' => true]];
    $response = $this->client->updateTrackingForOrder('O-1', '8MC585209K746392H-443844607820', $patch);

    $request = $this->mock->lastRequest();
    expect($response)->toBe([])
        ->and($request->getMethod())->toBe('PATCH')
        ->and((string) $request->getUri())->toEndWith('/v2/checkout/orders/O-1/trackers/8MC585209K746392H-443844607820')
        ->and(json_decode((string) $request->getBody(), true))->toBe($patch);
});

it('cancels order tracking', function () {
    $this->mock->addResponse(false, 204);

    $response = $this->client->cancelTrackingForOrder('O-1', 'TRACKER-1');

    $request = $this->mock->lastRequest();
    expect($response)->toBe([])
        ->and($request->getMethod())->toBe('PATCH')
        ->and((string) $request->getUri())->toEndWith('/v2/checkout/orders/O-1/trackers/TRACKER-1')
        ->and((string) $request->getBody())->toBe('[{"op":"replace","path":"\/status","value":"CANCELLED"}]');
});

it('returns PayPal errors when updating order tracking fails', function () {
    $this->mock->addResponse(['name' => 'UNPROCESSABLE_ENTITY'], 422);

    expect($this->client->cancelTrackingForOrder('O-1', 'TRACKER-1'))->toBe(['error' => ['name' => 'UNPROCESSABLE_ENTITY']]);
});
