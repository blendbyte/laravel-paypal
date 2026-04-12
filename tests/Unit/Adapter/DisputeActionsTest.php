<?php

it('can accept dispute claim', function () {
    $expectedResponse = $this->mockAcceptDisputesClaimResponse();

    $expectedMethod = 'acceptDisputeClaim';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-D-27803', 'Full refund to the customer.'))->toBe($expectedResponse);
});

it('can accept dispute offer resolution', function () {
    $expectedResponse = $this->mockAcceptDisputesOfferResolutionResponse();

    $expectedMethod = 'acceptDisputeOfferResolution';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-000-000-651-454', 'I am ok with the refund offered.'))->toBe($expectedResponse);
});

it('can acknowledge item is returned for raised dispute', function () {
    $expectedResponse = $this->mockAcknowledgeItemReturnedResponse();

    $expectedMethod = 'acknowledgeItemReturned';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-000-000-651-454', 'I have received the item back.', 'ITEM_RECEIVED'))->toBe($expectedResponse);
});

it('can send a message about a dispute', function () {
    $expectedResponse = $this->mockSendDisputeMessageResponse();

    $expectedMethod = 'sendDisputeMessage';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-000-000-651-454', 'I have shipped the item. Tracking number: 1234567890.'))->toBe($expectedResponse);
});
