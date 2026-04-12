<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can list disputes', function () {
    $expectedResponse = $this->mockListDisputesResponse();

    $expectedMethod = 'listDisputes';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can partially update a dispute', function () {
    $expectedResponse = '';

    $expectedParams = $this->updateDisputeParams();

    $expectedMethod = 'updateDispute';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-D-27803', $expectedParams))->toBe($expectedResponse);
});

it('can get details for a dispute', function () {
    $expectedResponse = $this->mockGetDisputesResponse();

    $expectedMethod = 'showDisputeDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-D-4012'))->toBe($expectedResponse);
});
