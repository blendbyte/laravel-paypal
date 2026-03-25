<?php

it('can list web hooks event types', function () {
    $expectedResponse = $this->mockListWebHookEventsTypesResponse();

    $expectedMethod = 'listEventTypes';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can list web hooks events', function () {
    $expectedResponse = $this->mockWebHookEventsListResponse();

    $expectedMethod = 'listEvents';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can show details for a web hooks event', function () {
    $expectedResponse = $this->mockGetWebHookEventResponse();

    $expectedMethod = 'showEventDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('8PT597110X687430LKGECATA'))->toBe($expectedResponse);
});

it('can resend notification for a web hooks event', function () {
    $expectedResponse = $this->mockResendWebHookEventNotificationResponse();

    $expectedParams = ['12334456'];

    $expectedMethod = 'resendEventNotification';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('8PT597110X687430LKGECATA', $expectedParams))->toBe($expectedResponse);
});
