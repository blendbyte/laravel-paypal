<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class WebHooksTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_create_a_web_hook(): void
    {
        $expectedResponse = $this->mockCreateWebHookResponse();

        $expectedMethod = 'createWebHook';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}(
            'https://example.com/example_webhook',
            ['PAYMENT.AUTHORIZATION.CREATED', 'PAYMENT.AUTHORIZATION.VOIDED']
        ));
    }

    #[Test]
    public function it_can_list_web_hooks(): void
    {
        $expectedResponse = $this->mockListWebHookResponse();

        $expectedMethod = 'listWebHooks';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }

    #[Test]
    public function it_can_delete_a_web_hook(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'deleteWebHook';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('5GP028458E2496506'));
    }

    #[Test]
    public function it_can_update_a_web_hook(): void
    {
        $expectedResponse = $this->mockUpdateWebHookResponse();

        $expectedParams = $this->mockUpdateWebHookParams();

        $expectedMethod = 'updateWebHook';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('0EH40505U7160970P', $expectedParams));
    }

    #[Test]
    public function it_can_show_details_for_a_web_hook(): void
    {
        $expectedResponse = $this->mockGetWebHookResponse();

        $expectedMethod = 'showWebHookDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('0EH40505U7160970P'));
    }

    #[Test]
    public function it_can_list_web_hooks_events(): void
    {
        $expectedResponse = $this->mockListWebHookEventsResponse();

        $expectedMethod = 'listWebHookEvents';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('0EH40505U7160970P'));
    }
}
