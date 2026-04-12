<?php

it('can show details for a captured payment', function () {
    $expectedResponse = $this->mockGetCapturedPaymentDetailsResponse();

    $expectedMethod = 'showCapturedPaymentDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('2GG279541U471931P'))->toBe($expectedResponse);
});

it('can refund a captured payment', function () {
    $expectedResponse = $this->mockRefundCapturedPaymentResponse();

    $expectedMethod = 'refundCapturedPayment';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(
        '2GG279541U471931P',
        'INVOICE-123',
        10.99,
        'Defective product'
    ))->toBe($expectedResponse);
});
