<?php

use Blendbyte\PayPal\Tests\MockRequestPayloads;

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
