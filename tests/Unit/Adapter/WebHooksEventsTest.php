<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class WebHooksEventsTest extends TestCase
{
    use MockClientClasses;
    use MockResponsePayloads;

    #[Test]
    public function it_can_list_web_hooks_event_types(): void
    {
        $expectedResponse = $this->mockListWebHookEventsTypesResponse();

        $expectedMethod = 'listEventTypes';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }

    #[Test]
    public function it_can_list_web_hooks_events(): void
    {
        $expectedResponse = $this->mockWebHookEventsListResponse();

        $expectedMethod = 'listEvents';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }

    #[Test]
    public function it_can_show_details_for_a_web_hooks_event(): void
    {
        $expectedResponse = $this->mockGetWebHookEventResponse();

        $expectedMethod = 'showEventDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('8PT597110X687430LKGECATA'));
    }

    #[Test]
    public function it_can_resend_notification_for_a_web_hooks_event(): void
    {
        $expectedResponse = $this->mockResendWebHookEventNotificationResponse();

        $expectedParams = ['12334456'];

        $expectedMethod = 'resendEventNotification';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('8PT597110X687430LKGECATA', $expectedParams));
    }
}
