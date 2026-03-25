<?php

it('can show details for an authorized payment', function () {
    $expectedResponse = $this->mockGetAuthorizedPaymentDetailsResponse();

    $expectedMethod = 'showAuthorizedPaymentDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('0VF52814937998046'))->toBe($expectedResponse);
});

it('can capture an authorized payment', function () {
    $expectedResponse = $this->mockCaptureAuthorizedPaymentResponse();

    $expectedMethod = 'captureAuthorizedPayment';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(
        '0VF52814937998046',
        'INVOICE-123',
        10.99,
        'Payment is due'
    ))->toBe($expectedResponse);
});

it('can reauthorize an authorized payment', function () {
    $expectedResponse = $this->mockReAuthorizeAuthorizedPaymentResponse();

    $expectedMethod = 'reAuthorizeAuthorizedPayment';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('0VF52814937998046', 10.99))->toBe($expectedResponse);
});

it('can void an authorized payment', function () {
    $expectedResponse = '';

    $expectedMethod = 'voidAuthorizedPayment';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('0VF52814937998046'))->toBe($expectedResponse);
});
