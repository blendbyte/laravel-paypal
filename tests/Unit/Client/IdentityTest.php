<?php

use GuzzleHttp\Utils;
use Blendbyte\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can user profile details', function () {
    $expectedResponse = $this->mockShowProfileInfoResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/identity/oauth2/userinfo?schema=paypalv1.1';
    $expectedParams = [
        'headers' => [
            'Accept'            => 'application/json',
            'Accept-Language'   => 'en_US',
            'Authorization'     => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
