<?php

namespace Blendbyte\PayPal\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Services\PayPal as PayPalClient;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class AdapterBillingPlansPricingHelpersTest extends TestCase
{
    use MockClientClasses;
    use MockResponsePayloads;

    /** @var string */
    protected static string $access_token = '';

    /** @var PayPalClient */
    protected PayPalClient $client;

    protected function setUp(): void
    {
        $this->client = new PayPalClient($this->getApiCredentials());

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAccessTokenResponse()
            )
        );
        $response = $this->client->getAccessToken();

        self::$access_token = $response['access_token'];

        parent::setUp();
    }

    #[Test]
    public function it_can_update_pricing_schemes_for_a_billing_plan(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client = $this->client->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
            ->addPricingScheme('DAY', 7, 0, true)
            ->addPricingScheme('MONTH', 1, 100);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->processBillingPlanPricingUpdates();

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_set_custom_limits_when_listing_billing_plans(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client = $this->client->setPageSize(30)
            ->showTotals(true);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListPlansResponse()
            )
        );

        $response = $this->client->setCurrentPage(1)->listPlans();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('plans', $response);
    }
}
