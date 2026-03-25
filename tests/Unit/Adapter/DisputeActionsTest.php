<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class DisputeActionsTest extends TestCase
{
    use MockClientClasses;
    use MockResponsePayloads;

    #[Test]
    public function it_can_accept_dispute_claim(): void
    {
        $expectedResponse = $this->mockAcceptDisputesClaimResponse();

        $expectedMethod = 'acceptDisputeClaim';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('PP-D-27803', 'Full refund to the customer.'));
    }

    #[Test]
    public function it_can_accept_dispute_offer_resolution(): void
    {
        $expectedResponse = $this->mockAcceptDisputesOfferResolutionResponse();

        $expectedMethod = 'acceptDisputeOfferResolution';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('PP-000-000-651-454', 'I am ok with the refund offered.'));
    }

    #[Test]
    public function it_can_acknowledge_item_is_returned_for_raised_dispute(): void
    {
        $expectedResponse = $this->mockAcknowledgeItemReturnedResponse();

        $expectedMethod = 'acknowledgeItemReturned';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('PP-000-000-651-454', 'I have received the item back.', 'ITEM_RECEIVED'));
    }
}
