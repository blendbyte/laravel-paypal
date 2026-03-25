<?php

use Blendbyte\PayPal\Tests\MockRequestPayloads;

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
