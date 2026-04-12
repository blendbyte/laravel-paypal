<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can get user profile details', function () {
    $expectedResponse = $this->mockShowProfileInfoResponse();

    $expectedMethod = 'showProfileInfo';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can list users', function () {
    $expectedResponse = $this->mocklistUsersResponse();
    $expectedMethod   = 'listUsers';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('userName'))->toBe($expectedResponse);
});

it('can show user details', function () {
    $expectedResponse = $this->mocklistUserResponse();
    $expectedMethod   = 'showUserDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('user-id-123'))->toBe($expectedResponse);
});

it('can delete a user', function () {
    $expectedResponse = '';
    $expectedMethod   = 'deleteUser';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('user-id-123'))->toBe($expectedResponse);
});

it('can create a merchant application', function () {
    $expectedResponse = $this->mockCreateMerchantApplicationResponse();
    $expectedMethod   = 'createMerchantApplication';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(
        'AGGREGATOR',
        ['https://example.com/callback'],
        ['facilitator@example.com'],
        'PAYER123',
        'false'
    ))->toBe($expectedResponse);
});

it('can set account properties', function () {
    $expectedResponse = $this->mockGetClientTokenResponse();
    $expectedMethod   = 'setAccountProperties';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(['PAYMENT']))->toBe($expectedResponse);
});

it('can disable account properties', function () {
    $expectedResponse = $this->mockGetClientTokenResponse();
    $expectedMethod   = 'disableAccountProperties';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('BRAINTREE_MERCHANT'))->toBe($expectedResponse);
});

it('can get client token', function () {
    $expectedResponse = $this->mockGetClientTokenResponse();
    $expectedMethod   = 'getClientToken';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can generate client token', function () {
    $expectedResponse = $this->mockGetClientTokenResponse();
    $expectedMethod   = 'generateClientToken';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});
