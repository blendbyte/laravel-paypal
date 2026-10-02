<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create partner referral', function () {
    $expectedResponse = $this->mockCreatePartnerReferralsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/customer/partner-referrals';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockCreatePartnerReferralParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(json_decode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can get referral details', function () {
    $expectedResponse = $this->mockShowReferralDataResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/customer/partner-referrals/ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=';
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

it('can list seller tracking information', function () {
    $expectedResponse = $this->mockListSellerTrackingInformationResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/partners/6LKMD2ML4NJYU/merchant-integrations?tracking_id=merchantref1';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(json_decode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can show seller status', function () {
    $expectedResponse = $this->mockShowSellerStatusResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/partners/6LKMD2ML4NJYU/merchant-integrations/8LQLM2ML4ZTYU';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(json_decode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can list merchant credentials', function () {
    $expectedResponse = $this->mockListMerchantCredentialsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/customer/partners/6LKMD2ML4NJYU/merchant-integrations/credentials';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(json_decode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
