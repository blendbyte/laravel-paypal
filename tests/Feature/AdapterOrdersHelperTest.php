<?php

namespace Blendbyte\PayPal\Tests\Feature;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Services\PayPal as PayPalClient;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class AdapterOrdersHelperTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
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
    public function it_can_confirm_payment_for_an_order(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $start_date = Carbon::now()->subDays(10)->toDateString();

        $this->client = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
            ->setBrandName('Test Brand')
            ->setStoredPaymentSource(
                'MERCHANT',
                'RECURRING',
                'SUBSEQUENT',
                true,
                '5TY05013RG002845M',
                $start_date,
                'Invoice-005',
                'VISA'
            );

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockConfirmOrderResponse()
            )
        );

        $response = $this->client->setupOrderConfirmation('5O190127TN364715T', 'ORDER_COMPLETE_ON_PAYMENT_APPROVAL');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }
}
