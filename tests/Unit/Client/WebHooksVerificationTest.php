<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Utils;

uses(MockRequestPayloads::class);

it('can verify web hook signature', function () {
    $expectedResponse = $this->mockVerifyWebHookSignatureResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/notifications/webhooks-event-types';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockVerifyWebHookSignatureParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
