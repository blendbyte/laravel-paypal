<?php

use GuzzleHttp\Utils;

it('can show details for a refund', function () {
    $expectedResponse = $this->mockGetRefundDetailsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/payments/refunds/1JU08902781691411';
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
