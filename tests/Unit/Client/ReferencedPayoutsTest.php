<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Utils;

uses(MockRequestPayloads::class);

it('can create referenced batch payout', function () {
    $expectedResponse = $this->mockCreateReferencedBatchPayoutResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
            'PayPal-Request-Id' => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ],
        'json' => $this->mockCreateReferencedBatchPayoutParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can list items referenced in batch payout', function () {
    $expectedResponse = $this->mockShowReferencedBatchPayoutResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts/KHbwO28lWlXwi2IlToJ2IYNG4juFv6kpbFx4J9oQ5Hb24RSp96Dk5FudVHd6v4E=';
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

it('can create referenced batch payout item', function () {
    $expectedResponse = $this->mockCreateReferencedBatchPayoutItemResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts-items';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
            'PayPal-Request-Id' => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ],
        'json' => $this->mockCreateReferencedBatchPayoutItemParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can show referenced payout item details', function () {
    $expectedResponse = $this->mockShowReferencedBatchPayoutItemResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts-items/CDZEC5MJ8R5HY';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
            'PayPal-Request-Id' => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ],
        'json' => $this->mockCreateReferencedBatchPayoutItemParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
