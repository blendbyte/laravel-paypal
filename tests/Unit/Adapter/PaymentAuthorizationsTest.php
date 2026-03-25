<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class PaymentAuthorizationsTest extends TestCase
{
    use MockClientClasses;
    use MockResponsePayloads;

    #[Test]
    public function it_can_show_details_for_an_authorized_payment(): void
    {
        $expectedResponse = $this->mockGetAuthorizedPaymentDetailsResponse();

        $expectedMethod = 'showAuthorizedPaymentDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('0VF52814937998046'));
    }

    #[Test]
    public function it_can_capture_an_authorized_payment(): void
    {
        $expectedResponse = $this->mockCaptureAuthorizedPaymentResponse();

        $expectedMethod = 'captureAuthorizedPayment';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}(
            '0VF52814937998046',
            'INVOICE-123',
            10.99,
            'Payment is due'
        ));
    }

    #[Test]
    public function it_can_reauthorize_an_authorized_payment(): void
    {
        $expectedResponse = $this->mockReAuthorizeAuthorizedPaymentResponse();

        $expectedMethod = 'reAuthorizeAuthorizedPayment';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('0VF52814937998046', 10.99));
    }

    #[Test]
    public function it_can_void_an_authorized_payment(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'voidAuthorizedPayment';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('0VF52814937998046'));
    }
}
