<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Utils;

uses(MockRequestPayloads::class);

it('can create a web hook', function () {
    $expectedResponse = $this->mockCreateWebHookResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/notifications/webhooks';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockCreateWebHookParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can list web hooks', function () {
    $expectedResponse = $this->mockListWebHookResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/notifications/webhooks';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can delete a web hook', function () {
    $expectedResponse = '';

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/notifications/webhooks/5GP028458E2496506';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'delete');

    expect(Utils::jsonDecode($mockHttpClient->delete($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can update a web hook', function () {
    $expectedResponse = $this->mockUpdateWebHookResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/notifications/webhooks/0EH40505U7160970P';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockUpdateWebHookParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'patch');

    expect(Utils::jsonDecode($mockHttpClient->patch($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can show details for a web hook', function () {
    $expectedResponse = $this->mockGetWebHookResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/notifications/webhooks/0EH40505U7160970P';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can list web hooks events', function () {
    $expectedResponse = $this->mockListWebHookEventsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/notifications/webhooks/0EH40505U7160970P';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
