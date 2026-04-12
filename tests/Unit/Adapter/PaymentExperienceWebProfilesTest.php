<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can list web experience profiles', function () {
    $expectedResponse = $this->mockListWebProfilesResponse();

    $expectedMethod = 'listWebExperienceProfiles';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can create web experience profile', function () {
    $expectedResponse = $this->mockWebProfileResponse();

    $expectedParams = $this->mockCreateWebProfileParams();

    $expectedMethod = 'createWebExperienceProfile';
    $additionalMethod = 'setRequestHeader';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();
    $mockClient->{$additionalMethod}('PayPal-Request-Id', 'some-request-id');

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can delete web experience profile', function () {
    $expectedResponse = '';

    $expectedParams = 'XP-A88A-LYLW-8Y3X-E5ER';

    $expectedMethod = 'deleteWebExperienceProfile';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can partially update web experience profile', function () {
    $expectedResponse = '';

    $expectedParams = $this->partiallyUpdateWebProfileParams();

    $expectedMethod = 'patchWebExperienceProfile';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('XP-A88A-LYLW-8Y3X-E5ER', $expectedParams))->toBe($expectedResponse);
});

it('can fully update web experience profile', function () {
    $expectedResponse = '';

    $expectedParams = $this->updateWebProfileParams();

    $expectedMethod = 'updateWebExperienceProfile';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('XP-A88A-LYLW-8Y3X-E5ER', $expectedParams))->toBe($expectedResponse);
});

it('can get web experience profile details', function () {
    $expectedResponse = $this->mockWebProfileResponse();

    $expectedParams = 'XP-A88A-LYLW-8Y3X-E5ER';

    $expectedMethod = 'showWebExperienceProfileDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});
