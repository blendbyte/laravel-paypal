<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create partner referral', function () {
    $expectedResponse = $this->mockCreatePartnerReferralsResponse();

    $expectedParams = $this->mockCreatePartnerReferralParams();

    $expectedMethod = 'createPartnerReferral';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can get referral details', function () {
    $expectedResponse = $this->mockShowReferralDataResponse();

    $expectedParams = 'ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=';

    $expectedMethod = 'showReferralData';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can list seller tracking information', function () {
    $expectedResponse = $this->mockListSellerTrackingInformationResponse();
    $expectedMethod   = 'listSellerTrackingInformation';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('6LKMD2ML4NJYU', 'merchantref1'))->toBe($expectedResponse);
});

it('can show seller status', function () {
    $expectedResponse = $this->mockShowSellerStatusResponse();
    $expectedMethod   = 'showSellerStatus';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('6LKMD2ML4NJYU', '8LQLM2ML4ZTYU'))->toBe($expectedResponse);
});

it('can list merchant credentials', function () {
    $expectedResponse = $this->mockListMerchantCredentialsResponse();
    $expectedMethod   = 'listMerchantCredentials';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('6LKMD2ML4NJYU'))->toBe($expectedResponse);
});
