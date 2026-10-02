<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can list disputes', function () {
    $expectedResponse = $this->mockListDisputesResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/disputes/';
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

it('can partially update a dispute', function () {
    $expectedResponse = '';

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-D-27803';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->updateDisputeParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'patch');

    expect(json_decode($mockHttpClient->patch($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can get details for a dispute', function () {
    $expectedResponse = $this->mockGetDisputesResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-D-4012';
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
