<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can get tracking details for tracking id', function () {
    $expectedResponse = $this->mockGetTrackingDetailsResponse();

    $expectedParams = '8MC585209K746392H-443844607820';

    $expectedMethod = 'showTrackingDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can update tracking details for tracking id', function () {
    $expectedResponse = '';

    $expectedData = $this->mockUpdateTrackingDetailsParams();

    $expectedParams = '8MC585209K746392H-443844607820';

    $expectedMethod = 'updateTrackingDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams, $expectedData))->toBe($expectedResponse);
});

it('can create tracking in batches', function () {
    $expectedResponse = $this->mockCreateTrackinginBatchesResponse();

    $expectedParams = $this->mockCreateTrackinginBatchesParams();

    $expectedMethod = 'addBatchTracking';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can add tracking for a single transaction', function () {
    $expectedResponse = $this->mockCreateTrackinginBatchesResponse();
    $expectedParams   = $this->mockUpdateTrackingDetailsParams();
    $expectedMethod   = 'addTracking';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can list tracking details', function () {
    $expectedResponse = $this->mockGetTrackingDetailsResponse();
    $expectedMethod   = 'listTrackingDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('8MC585209K746392H', '443844607820'))->toBe($expectedResponse);
});
