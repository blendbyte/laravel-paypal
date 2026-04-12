<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create a billing agreement token', function () {
    $expectedResponse = $this->mockCreateBillingAgreementTokenResponse();
    $expectedParams   = $this->createBillingAgreementTokenParams();
    $expectedMethod   = 'createBillingAgreementToken';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can get billing agreement token details', function () {
    $expectedResponse = $this->mockGetBillingAgreementTokenResponse();
    $expectedMethod   = 'getBillingAgreementTokenDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('BA-8A802366G0648845Y'))->toBe($expectedResponse);
});

it('can create a billing agreement', function () {
    $expectedResponse = $this->mockCreateBillingAgreementResponse();
    $expectedMethod   = 'createBillingAgreement';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('BA-8A802366G0648845Y'))->toBe($expectedResponse);
});

it('can update a billing agreement', function () {
    $expectedResponse = '';
    $expectedParams   = $this->updateBillingAgreementParams();
    $expectedMethod   = 'updateBillingAgreement';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('B-50V812176H0783741', $expectedParams))->toBe($expectedResponse);
});

it('can show billing agreement details', function () {
    $expectedResponse = $this->mockShowBillingAgreementResponse();
    $expectedMethod   = 'showBillingAgreementDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('B-50V812176H0783741'))->toBe($expectedResponse);
});

it('can cancel a billing agreement', function () {
    $expectedResponse = '';
    $expectedMethod   = 'cancelBillingAgreement';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('B-50V812176H0783741'))->toBe($expectedResponse);
});
