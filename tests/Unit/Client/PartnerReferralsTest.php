<?php

use GuzzleHttp\Utils;
use Blendbyte\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create partner referral', function () {
    $expectedResponse = $this->mockCreatePartnerReferralsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/customer/partner-referrals';
    $expectedParams = [
        'headers' => [
            'Accept'            => 'application/json',
            'Accept-Language'   => 'en_US',
            'Authorization'     => 'Bearer some-token',
        ],
        'json' => $this->mockCreatePartnerReferralParams(),
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can get referral details', function () {
    $expectedResponse = $this->mockShowReferralDataResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/customer/partner-referrals/ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=';
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
