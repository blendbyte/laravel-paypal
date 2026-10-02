<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can show details for a captured payment', function () {
    $expectedResponse = $this->mockGetCapturedPaymentDetailsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/payments/captures/2GG279541U471931P';
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

it('can refund a captured payment', function () {
    $expectedResponse = $this->mockRefundCapturedPaymentResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/payments/captures/2GG279541U471931P/refund';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockRefundCapturedPaymentParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(json_decode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
