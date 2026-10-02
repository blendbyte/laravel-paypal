<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create batch payout', function () {
    $expectedResponse = $this->mockCreateBatchPayoutResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/payouts';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockCreateBatchPayoutParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(json_decode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can show batch payout details', function () {
    $expectedResponse = $this->showBatchPayoutResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/payouts/FYXMPQTX4JC9N';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(json_decode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can show batch payout item details', function () {
    $expectedResponse = $this->showBatchPayoutItemResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/payouts-item/8AELMXH8UB2P8';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(json_decode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can cancel unclaimed batch payout item', function () {
    $expectedResponse = $this->mockCancelUnclaimedBatchItemResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/payouts-item/8AELMXH8UB2P8/cancel';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(json_decode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
