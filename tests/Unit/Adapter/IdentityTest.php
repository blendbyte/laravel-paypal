<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class IdentityTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_get_user_profile_details(): void
    {
        $expectedResponse = $this->mockShowProfileInfoResponse();

        $expectedMethod = 'showProfileInfo';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }
}
