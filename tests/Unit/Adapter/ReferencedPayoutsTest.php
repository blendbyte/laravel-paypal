<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class ReferencedPayoutsTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_create_referenced_batch_payout(): void
    {
        $expectedResponse = $this->mockCreateReferencedBatchPayoutResponse();

        $expectedParams = $this->mockCreateReferencedBatchPayoutParams();

        $expectedMethod = 'createReferencedBatchPayout';
        $additionalMethod = 'setRequestHeaders';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();
        $mockClient->{$additionalMethod}([
            'PayPal-Request-Id'             => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ]);

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_list_items_referenced_in_batch_payout(): void
    {
        $expectedResponse = $this->mockShowReferencedBatchPayoutResponse();

        $expectedParams = 'KHbwO28lWlXwi2IlToJ2IYNG4juFv6kpbFx4J9oQ5Hb24RSp96Dk5FudVHd6v4E=';

        $expectedMethod = 'listItemsReferencedInBatchPayout';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_create_referenced_batch_payout_item(): void
    {
        $expectedResponse = $this->mockCreateReferencedBatchPayoutItemResponse();

        $expectedParams = $this->mockCreateReferencedBatchPayoutItemParams();

        $expectedMethod = 'createReferencedBatchPayoutItem';
        $additionalMethod = 'setRequestHeaders';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();
        $mockClient->{$additionalMethod}([
            'PayPal-Request-Id'             => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ]);

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_show_referenced_payout_item_details(): void
    {
        $expectedResponse = $this->mockShowReferencedBatchPayoutItemResponse();

        $expectedParams = 'CDZEC5MJ8R5HY';

        $expectedMethod = 'showReferencedPayoutItemDetails';
        $additionalMethod = 'setRequestHeader';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();
        $mockClient->{$additionalMethod}('PayPal-Partner-Attribution-Id', 'some-attribution-id');

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }
}
