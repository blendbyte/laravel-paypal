<?php

use Blendbyte\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Utils;

uses(MockRequestPayloads::class);

it('can accept dispute claim', function () {
    $expectedResponse = $this->mockAcceptDisputesClaimResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-D-27803/accept-claim';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->acceptDisputeClaimParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can accept dispute offer resolution', function () {
    $expectedResponse = $this->mockAcceptDisputesOfferResolutionResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-000-000-651-454/accept-offer';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->acceptDisputeResolutionParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can acknowledge item is returned for raised dispute', function () {
    $expectedResponse = $this->mockAcknowledgeItemReturnedResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-000-000-651-454/acknowledge-return-item';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->acknowledgeItemReturnedParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
