<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can show details for an authorized payment', function () {
    $expectedResponse = $this->mockGetAuthorizedPaymentDetailsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/payments/authorizations/0VF52814937998046';
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

it('can capture an authorized payment', function () {
    $expectedResponse = $this->mockCaptureAuthorizedPaymentResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/payments/authorizations/0VF52814937998046/capture';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockCaptureAuthorizedPaymentParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(json_decode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can reauthorize an authorized payment', function () {
    $expectedResponse = $this->mockReAuthorizeAuthorizedPaymentResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/payments/authorizations/0VF52814937998046/reauthorize';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockReAuthorizeAuthorizedPaymentParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(json_decode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can void an authorized payment', function () {
    $expectedResponse = '';

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/payments/authorizations/0VF52814937998046/void';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(json_decode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
