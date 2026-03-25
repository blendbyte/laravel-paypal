<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class BillingPlansTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_create_a_billing_plan(): void
    {
        $expectedResponse = $this->mockCreatePlansResponse();

        $expectedParams = $this->createPlanParams();

        $expectedMethod = 'createPlan';
        $additionalMethod = 'setRequestHeader';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();
        $mockClient->{$additionalMethod}('PayPal-Request-Id', 'some-request-id');

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams, 'some-request-id'));
    }

    #[Test]
    public function it_can_list_billing_plans(): void
    {
        $expectedResponse = $this->mockListPlansResponse();

        $expectedMethod = 'listPlans';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}(1, 2, true));
    }

    #[Test]
    public function it_can_update_a_billing_plan(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->updatePlanParams();

        $expectedMethod = 'updatePlan';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('P-7GL4271244454362WXNWU5NQ', $expectedParams));
    }

    #[Test]
    public function it_can_show_details_for_a_billing_plan(): void
    {
        $expectedResponse = $this->mockGetPlansResponse();

        $expectedMethod = 'showPlanDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('P-7GL4271244454362WXNWU5NQ'));
    }

    #[Test]
    public function it_can_activate_a_billing_plan(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'activatePlan';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('P-7GL4271244454362WXNWU5NQ'));
    }

    #[Test]
    public function it_can_deactivate_a_billing_plan(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'deactivatePlan';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('P-7GL4271244454362WXNWU5NQ'));
    }

    #[Test]
    public function it_can_update_pricing_for_a_billing_plan(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->updatePlanPricingParams();

        $expectedMethod = 'updatePlanPricing';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('P-2UF78835G6983425GLSM44MA', $expectedParams));
    }
}
