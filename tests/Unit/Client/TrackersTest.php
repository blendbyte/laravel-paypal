<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Utils;

uses(MockRequestPayloads::class);

it('can get tracking details for tracking id', function () {
    $expectedResponse = $this->mockGetTrackingDetailsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/shipping/trackers/8MC585209K746392H-443844607820';
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

it('can update tracking details for tracking id', function () {
    $expectedResponse = '';

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/shipping/trackers/8MC585209K746392H-443844607820';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockUpdateTrackingDetailsParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'put');

    expect(Utils::jsonDecode($mockHttpClient->put($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can create tracking in batches', function () {
    $expectedResponse = $this->mockCreateTrackinginBatchesResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/shipping/trackers-batch';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockCreateTrackinginBatchesParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can add tracking for a single transaction', function () {
    $expectedResponse = $this->mockCreateTrackinginBatchesResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/shipping/trackers';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
        'json' => $this->mockUpdateTrackingDetailsParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can list tracking details', function () {
    $expectedResponse = $this->mockGetTrackingDetailsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/shipping/trackers?transaction_id=8MC585209K746392H&tracking_number=443844607820';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
