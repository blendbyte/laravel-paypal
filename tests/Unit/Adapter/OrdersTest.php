<?php

use Blendbyte\PayPal\Tests\MockRequestPayloads;

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
