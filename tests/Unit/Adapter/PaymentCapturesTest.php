<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class PaymentCapturesTest extends TestCase
{
    use MockClientClasses;
    use MockResponsePayloads;

    #[Test]
    public function it_can_show_details_for_a_captured_payment(): void
    {
        $expectedResponse = $this->mockGetCapturedPaymentDetailsResponse();

        $expectedMethod = 'showCapturedPaymentDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('2GG279541U471931P'));
    }

    #[Test]
    public function it_can_refund_a_captured_payment(): void
    {
        $expectedResponse = $this->mockRefundCapturedPaymentResponse();

        $expectedMethod = 'refundCapturedPayment';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}(
            '2GG279541U471931P',
            'INVOICE-123',
            10.99,
            'Defective product'
        ));
    }
}
