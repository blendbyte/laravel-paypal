<?php

use Srmklive\PayPal\Testing\MockPayPalClient;
use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

describe('MockPayPalClient', function () {
    it('queues and returns a response', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => '5O190127TN364715T', 'status' => 'CREATED']);

        $provider = $mock->mockProvider();
        $response = $provider->createOrder($this->createOrderParams());

        expect($response)->toHaveKey('id', '5O190127TN364715T');
        expect($response)->toHaveKey('status', 'CREATED');
    });

    it('queues multiple responses in order', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'ORDER-1', 'status' => 'CREATED']);
        $mock->addResponse(['id' => 'ORDER-2', 'status' => 'CREATED']);

        $provider = $mock->mockProvider();

        $first = $provider->createOrder($this->createOrderParams());
        $second = $provider->createOrder($this->createOrderParams());

        expect($first['id'])->toBe('ORDER-1');
        expect($second['id'])->toBe('ORDER-2');
    });

    it('captures request count', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'ORDER-1', 'status' => 'CREATED']);
        $mock->addResponse(['id' => 'ORDER-2', 'status' => 'CREATED']);

        $provider = $mock->mockProvider();
        $provider->createOrder($this->createOrderParams());
        $provider->createOrder($this->createOrderParams());

        expect($mock->requestCount())->toBe(2);
    });

    it('exposes all captured requests', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'ORDER-1', 'status' => 'CREATED']);

        $provider = $mock->mockProvider();
        $provider->createOrder($this->createOrderParams());

        $requests = $mock->requests();
        expect($requests)->toHaveCount(1);
        expect($requests[0]->getMethod())->toBe('POST');
    });

    it('exposes the last request for assertion', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'ORDER-1', 'status' => 'CREATED']);

        $provider = $mock->mockProvider();
        $provider->createOrder($this->createOrderParams());

        $request = $mock->lastRequest();
        expect($request)->not->toBeNull();
        expect($request->getHeaderLine('Authorization'))->toBe('Bearer mock-access-token');
        expect($request->getHeaderLine('Content-Type'))->toBe('application/json');
    });

    it('returns null for lastRequest when no calls made', function () {
        $mock = new MockPayPalClient();
        expect($mock->lastRequest())->toBeNull();
    });

    it('handles empty body response for no-content operations', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(false, 204);

        $provider = $mock->mockProvider();
        $response = $provider->updateOrder('5O190127TN364715T', $this->updateOrderParams());

        expect($response)->toBeEmpty();
    });

    it('sends requests to the correct PayPal endpoint', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'ORDER-1', 'status' => 'CREATED']);

        $provider = $mock->mockProvider();
        $provider->createOrder($this->createOrderParams());

        $uri = $mock->lastRequest()->getUri();
        expect((string) $uri)->toContain('/v2/checkout/orders');
    });

    it('accepts custom config via mockProvider', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'ORDER-1', 'status' => 'CREATED']);

        $provider = $mock->mockProvider(['currency' => 'EUR']);
        $provider->createOrder($this->createOrderParams());

        expect($mock->requestCount())->toBe(1);
    });

    it('throws when sendRequest is called with no queued responses', function () {
        $mock = new MockPayPalClient();
        $mock->sendRequest(new \GuzzleHttp\Psr7\Request('GET', 'https://example.com'));
    })->throws(\UnderflowException::class);
});
