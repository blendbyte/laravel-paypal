<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create an order', function () {
    $expectedResponse = $this->mockCreateOrdersResponse();

    $expectedParams = $this->createOrderParams();

    $expectedMethod = 'createOrder';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can update an order', function () {
    $expectedResponse = '';

    $expectedParams = $this->updateOrderParams();

    $expectedMethod = 'updateOrder';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('5O190127TN364715T', $expectedParams))->toBe($expectedResponse);
});

it('can show order details', function () {
    $expectedResponse = $this->mockOrderDetailsResponse();

    $expectedMethod = 'showOrderDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('5O190127TN364715T'))->toBe($expectedResponse);
});

it('can authorize payment for an order', function () {
    $expectedResponse = $this->mockOrderPaymentAuthorizedResponse();

    $expectedMethod = 'authorizePaymentOrder';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('5O190127TN364715T'))->toBe($expectedResponse);
});

it('can capture payment for an order', function () {
    $expectedResponse = $this->mockOrderPaymentCapturedResponse();

    $expectedMethod = 'capturePaymentOrder';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('5O190127TN364715T'))->toBe($expectedResponse);
});

it('can add tracking for an order', function () {
    $expectedResponse = $this->mockAddTrackingForOrderResponse();

    $expectedParams = $this->addTrackingForOrderParams();

    $expectedMethod = 'addTrackingForOrder';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('5O190127TN364715T', $expectedParams))->toBe($expectedResponse);
});
