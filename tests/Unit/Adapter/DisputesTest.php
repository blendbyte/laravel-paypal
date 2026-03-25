<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class DisputesTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_list_disputes(): void
    {
        $expectedResponse = $this->mockListDisputesResponse();

        $expectedMethod = 'listDisputes';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }

    #[Test]
    public function it_can_partially_update_a_dispute(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->updateDisputeParams();

        $expectedMethod = 'updateDispute';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('PP-D-27803', $expectedParams));
    }

    #[Test]
    public function it_can_get_details_for_a_dispute(): void
    {
        $expectedResponse = $this->mockGetDisputesResponse();

        $expectedMethod = 'showDisputeDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('PP-D-4012'));
    }
}
