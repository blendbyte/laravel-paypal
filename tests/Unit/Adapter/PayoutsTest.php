<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class PayoutsTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_create_batch_payout(): void
    {
        $expectedResponse = $this->mockCreateBatchPayoutResponse();

        $expectedParams = $this->mockCreateBatchPayoutParams();

        $expectedMethod = 'createBatchPayout';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_show_batch_payout_details(): void
    {
        $expectedResponse = $this->showBatchPayoutResponse();

        $expectedParams = 'FYXMPQTX4JC9N';

        $expectedMethod = 'showBatchPayoutDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_show_batch_payout_item_details(): void
    {
        $expectedResponse = $this->showBatchPayoutItemResponse();

        $expectedParams = '8AELMXH8UB2P8';

        $expectedMethod = 'showPayoutItemDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_cancel_unclaimed_batch_payout_item(): void
    {
        $expectedResponse = $this->mockCancelUnclaimedBatchItemResponse();

        $expectedParams = '8AELMXH8UB2P8';

        $expectedMethod = 'cancelUnclaimedPayoutItem';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }
}
