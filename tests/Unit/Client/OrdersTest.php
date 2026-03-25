<?php

use Blendbyte\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Utils;

uses(MockRequestPayloads::class);

it('can create an order', function () {
    $expectedResponse = $this->mockCreateOrdersResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/checkout/orders';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->createOrderParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
