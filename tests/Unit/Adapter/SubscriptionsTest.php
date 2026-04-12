<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create a subscription', function () {
    $expectedResponse = $this->mockCreateSubscriptionResponse();

    $expectedParams = $this->mockCreateSubscriptionParams();

    $expectedMethod = 'createSubscription';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can update a subscription', function () {
    $expectedResponse = '';

    $expectedParams = $this->mockUpdateSubscriptionParams();

    $expectedMethod = 'updateSubscription';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('I-BW452GLLEP1G', $expectedParams))->toBe($expectedResponse);
});

it('can show details for a subscription', function () {
    $expectedResponse = $this->mockGetSubscriptionDetailsResponse();

    $expectedMethod = 'showSubscriptionDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('I-BW452GLLEP1G'))->toBe($expectedResponse);
});

it('can activate a subscription', function () {
    $expectedResponse = '';

    $expectedMethod = 'activateSubscription';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('I-BW452GLLEP1G', 'Reactivating the subscription'))->toBe($expectedResponse);
});

it('can cancel a subscription', function () {
    $expectedResponse = '';

    $expectedMethod = 'cancelSubscription';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('I-BW452GLLEP1G', 'Not satisfied with the service'))->toBe($expectedResponse);
});

it('can suspend a subscription', function () {
    $expectedResponse = '';

    $expectedMethod = 'suspendSubscription';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('I-BW452GLLEP1G', 'Item out of stock'))->toBe($expectedResponse);
});

it('can capture payment for a subscription', function () {
    $expectedResponse = '';

    $expectedMethod = 'captureSubscriptionPayment';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('I-BW452GLLEP1G', 'Charging as the balance reached the limit', 100))->toBe($expectedResponse);
});

it('can update quantity or product for a subscription', function () {
    $expectedResponse = $this->mockUpdateSubscriptionItemsResponse();

    $expectedParams = $this->mockUpdateSubscriptionItemsParams();

    $expectedMethod = 'reviseSubscription';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('I-BW452GLLEP1G', $expectedParams))->toBe($expectedResponse);
});

it('can list transactions for a subscription', function () {
    $expectedResponse = $this->mockListSubscriptionTransactionsResponse();

    $expectedMethod = 'listSubscriptionTransactions';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('I-BW452GLLEP1G', '2018-01-21T07:50:20.940Z', '2018-08-22T07:50:20.940Z'))->toBe($expectedResponse);
});
