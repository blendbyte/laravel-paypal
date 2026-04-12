<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create a web hook', function () {
    $expectedResponse = $this->mockCreateWebHookResponse();

    $expectedMethod = 'createWebHook';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(
        'https://example.com/example_webhook',
        ['PAYMENT.AUTHORIZATION.CREATED', 'PAYMENT.AUTHORIZATION.VOIDED']
    ))->toBe($expectedResponse);
});

it('can list web hooks', function () {
    $expectedResponse = $this->mockListWebHookResponse();

    $expectedMethod = 'listWebHooks';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can delete a web hook', function () {
    $expectedResponse = '';

    $expectedMethod = 'deleteWebHook';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('5GP028458E2496506'))->toBe($expectedResponse);
});

it('can update a web hook', function () {
    $expectedResponse = $this->mockUpdateWebHookResponse();

    $expectedParams = $this->mockUpdateWebHookParams();

    $expectedMethod = 'updateWebHook';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('0EH40505U7160970P', $expectedParams))->toBe($expectedResponse);
});

it('can show details for a web hook', function () {
    $expectedResponse = $this->mockGetWebHookResponse();

    $expectedMethod = 'showWebHookDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('0EH40505U7160970P'))->toBe($expectedResponse);
});

it('can list web hooks events', function () {
    $expectedResponse = $this->mockListWebHookEventsResponse();

    $expectedMethod = 'listWebHookEvents';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('0EH40505U7160970P'))->toBe($expectedResponse);
});
