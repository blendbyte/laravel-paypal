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

it('can make an offer to resolve a dispute', function () {
    $expectedResponse = $this->mockAcceptDisputesClaimResponse();
    $expectedMethod   = 'makeOfferToResolveDispute';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-000-000-651-454', 'Full refund to the customer.', 10.00, 'REFUND'))->toBe($expectedResponse);
});

it('can escalate a dispute to a claim', function () {
    $expectedResponse = $this->mockAcceptDisputesClaimResponse();
    $expectedMethod   = 'escalateDisputeToClaim';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-000-000-651-454', 'Escalating to a claim due to non-resolution.'))->toBe($expectedResponse);
});

it('can update dispute status', function () {
    $expectedResponse = $this->mockAcceptDisputesClaimResponse();
    $expectedMethod   = 'updateDisputeStatus';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-000-000-651-454', true))->toBe($expectedResponse);
});

it('can settle a dispute', function () {
    $expectedResponse = $this->mockAcceptDisputesClaimResponse();
    $expectedMethod   = 'settleDispute';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-000-000-651-454', true))->toBe($expectedResponse);
});

it('can decline a dispute offer resolution', function () {
    $expectedResponse = $this->mockAcceptDisputesClaimResponse();
    $expectedMethod   = 'declineDisputeOfferResolution';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('PP-000-000-651-454', 'I do not agree with the offer.'))->toBe($expectedResponse);
});
