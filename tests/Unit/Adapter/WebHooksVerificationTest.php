<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can verify web hook signature', function () {
    $expectedResponse = $this->mockVerifyWebHookSignatureResponse();

    $expectedParams = $this->mockVerifyWebHookSignatureParams();

    $expectedMethod = 'verifyWebHook';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});
