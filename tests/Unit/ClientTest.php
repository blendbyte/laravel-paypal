<?php

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Utils;

it('can be instantiated', function () {
    $client = new HttpClient;
    expect($client)->toBeInstanceOf(HttpClient::class);
});

it('can get access token', function () {
    $expectedResponse = $this->mockAccessTokenResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/oauth2/token?grant_type=client_credentials';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
        ],
        'auth' => ['username', 'password'],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams);

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
