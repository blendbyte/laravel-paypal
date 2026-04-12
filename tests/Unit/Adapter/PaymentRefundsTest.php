<?php

it('can show details for a refund', function () {
    $expectedResponse = $this->mockGetRefundDetailsResponse();

    $expectedMethod = 'showRefundDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('1JU08902781691411'))->toBe($expectedResponse);
});
