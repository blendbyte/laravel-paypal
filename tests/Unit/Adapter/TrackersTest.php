<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class TrackersTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_get_tracking_details_for_tracking_id(): void
    {
        $expectedResponse = $this->mockGetTrackingDetailsResponse();

        $expectedParams = '8MC585209K746392H-443844607820';

        $expectedMethod = 'showTrackingDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_update_tracking_details_for_tracking_id(): void
    {
        $expectedResponse = '';

        $expectedData = $this->mockUpdateTrackingDetailsParams();

        $expectedParams = '8MC585209K746392H-443844607820';

        $expectedMethod = 'updateTrackingDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams, $expectedData));
    }

    #[Test]
    public function it_can_create_tracking_in_batches(): void
    {
        $expectedResponse = $this->mockCreateTrackinginBatchesResponse();

        $expectedParams = $this->mockCreateTrackinginBatchesParams();

        $expectedMethod = 'addBatchTracking';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }
}
