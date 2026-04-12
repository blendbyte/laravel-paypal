<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create batch payout', function () {
    $expectedResponse = $this->mockCreateBatchPayoutResponse();

    $expectedParams = $this->mockCreateBatchPayoutParams();

    $expectedMethod = 'createBatchPayout';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can show batch payout details', function () {
    $expectedResponse = $this->showBatchPayoutResponse();

    $expectedParams = 'FYXMPQTX4JC9N';

    $expectedMethod = 'showBatchPayoutDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can show batch payout item details', function () {
    $expectedResponse = $this->showBatchPayoutItemResponse();

    $expectedParams = '8AELMXH8UB2P8';

    $expectedMethod = 'showPayoutItemDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can cancel unclaimed batch payout item', function () {
    $expectedResponse = $this->mockCancelUnclaimedBatchItemResponse();

    $expectedParams = '8AELMXH8UB2P8';

    $expectedMethod = 'cancelUnclaimedPayoutItem';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});
