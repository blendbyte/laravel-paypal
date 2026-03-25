<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class SubscriptionsTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_create_a_subscription(): void
    {
        $expectedResponse = $this->mockCreateSubscriptionResponse();

        $expectedParams = $this->mockCreateSubscriptionParams();

        $expectedMethod = 'createSubscription';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_update_a_subscription(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->mockUpdateSubscriptionParams();

        $expectedMethod = 'updateSubscription';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('I-BW452GLLEP1G', $expectedParams));
    }

    #[Test]
    public function it_can_show_details_for_a_subscription(): void
    {
        $expectedResponse = $this->mockGetSubscriptionDetailsResponse();

        $expectedMethod = 'showSubscriptionDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('I-BW452GLLEP1G'));
    }

    #[Test]
    public function it_can_activate_a_subscription(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'activateSubscription';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('I-BW452GLLEP1G', 'Reactivating the subscription'));
    }

    #[Test]
    public function it_can_cancel_a_subscription(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'cancelSubscription';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('I-BW452GLLEP1G', 'Not satisfied with the service'));
    }

    #[Test]
    public function it_can_suspend_a_subscription(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'suspendSubscription';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('I-BW452GLLEP1G', 'Item out of stock'));
    }

    #[Test]
    public function it_can_capture_payment_for_a_subscription(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'captureSubscriptionPayment';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('I-BW452GLLEP1G', 'Charging as the balance reached the limit', 100));
    }

    #[Test]
    public function it_can_update_quantity_or_product_for_a_subscription(): void
    {
        $expectedResponse = $this->mockUpdateSubscriptionItemsResponse();

        $expectedParams = $this->mockUpdateSubscriptionItemsParams();

        $expectedMethod = 'reviseSubscription';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('I-BW452GLLEP1G', $expectedParams));
    }

    #[Test]
    public function it_can_list_transactions_for_a_subscription(): void
    {
        $expectedResponse = $this->mockListSubscriptionTransactionsResponse();

        $expectedMethod = 'listSubscriptionTransactions';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('I-BW452GLLEP1G', '2018-01-21T07:50:20.940Z', '2018-08-22T07:50:20.940Z'));
    }
}
