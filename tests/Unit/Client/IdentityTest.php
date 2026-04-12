<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Utils;

uses(MockRequestPayloads::class);

it('can user profile details', function () {
    $expectedResponse = $this->mockShowProfileInfoResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/identity/oauth2/userinfo?schema=paypalv1.1';
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

it('can list users', function () {
    $expectedResponse = $this->mocklistUsersResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/scim/Users?filter=userName';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can show user details', function () {
    $expectedResponse = $this->mocklistUserResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/scim/Users/user-id-123';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can delete a user', function () {
    $expectedResponse = '';

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/scim/Users/user-id-123';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'delete');

    expect(Utils::jsonDecode($mockHttpClient->delete($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can create a merchant application', function () {
    $expectedResponse = $this->mockCreateMerchantApplicationResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/identity/applications';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
        'json' => [
            'application_type' => 'web',
            'redirect_uris'    => ['https://example.com/callback'],
            'client_name'      => 'AGGREGATOR',
            'contacts'         => ['facilitator@example.com'],
            'payer_id'         => 'PAYER123',
            'migrated_app'     => 'false',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can set account properties', function () {
    $expectedResponse = $this->mockGetClientTokenResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/identity/account-settings';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
        'json' => [
            'account_property' => 'BRAINTREE_MERCHANT',
            'features'         => ['PAYMENT'],
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can disable account properties', function () {
    $expectedResponse = $this->mockGetClientTokenResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/identity/account-settings/deactivate';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
        'json' => ['account_property' => 'BRAINTREE_MERCHANT'],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can get client token', function () {
    $expectedResponse = $this->mockGetClientTokenResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/identity/generate-token';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can generate client token', function () {
    $expectedResponse = $this->mockGetClientTokenResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/identity/generate-token';
    $expectedParams   = [
        'headers' => [
            'Accept'          => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization'   => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
