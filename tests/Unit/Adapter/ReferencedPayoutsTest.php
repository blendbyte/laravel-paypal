<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create referenced batch payout', function () {
    $expectedResponse = $this->mockCreateReferencedBatchPayoutResponse();

    $expectedParams = $this->mockCreateReferencedBatchPayoutParams();

    $expectedMethod = 'createReferencedBatchPayout';
    $additionalMethod = 'setRequestHeaders';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();
    $mockClient->{$additionalMethod}([
        'PayPal-Request-Id' => 'some-request-id',
        'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
    ]);

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can list items referenced in batch payout', function () {
    $expectedResponse = $this->mockShowReferencedBatchPayoutResponse();

    $expectedParams = 'KHbwO28lWlXwi2IlToJ2IYNG4juFv6kpbFx4J9oQ5Hb24RSp96Dk5FudVHd6v4E=';

    $expectedMethod = 'listItemsReferencedInBatchPayout';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can create referenced batch payout item', function () {
    $expectedResponse = $this->mockCreateReferencedBatchPayoutItemResponse();

    $expectedParams = $this->mockCreateReferencedBatchPayoutItemParams();

    $expectedMethod = 'createReferencedBatchPayoutItem';
    $additionalMethod = 'setRequestHeaders';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();
    $mockClient->{$additionalMethod}([
        'PayPal-Request-Id' => 'some-request-id',
        'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
    ]);

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can show referenced payout item details', function () {
    $expectedResponse = $this->mockShowReferencedBatchPayoutItemResponse();

    $expectedParams = 'CDZEC5MJ8R5HY';

    $expectedMethod = 'showReferencedPayoutItemDetails';
    $additionalMethod = 'setRequestHeader';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();
    $mockClient->{$additionalMethod}('PayPal-Partner-Attribution-Id', 'some-attribution-id');

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});
