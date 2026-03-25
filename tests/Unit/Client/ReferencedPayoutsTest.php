<?php

namespace Blendbyte\PayPal\Tests\Unit\Client;

use GuzzleHttp\Utils;
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

        $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts';
        $expectedParams = [
            'headers' => [
                'Accept'                        => 'application/json',
                'Accept-Language'               => 'en_US',
                'Authorization'                 => 'Bearer some-token',
                'PayPal-Request-Id'             => 'some-request-id',
                'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
            ],
            'json' => $this->mockCreateReferencedBatchPayoutParams(),
        ];

        $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

        $this->assertEquals($expectedResponse, Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true));
    }

    #[Test]
    public function it_can_list_items_referenced_in_batch_payout(): void
    {
        $expectedResponse = $this->mockShowReferencedBatchPayoutResponse();

        $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts/KHbwO28lWlXwi2IlToJ2IYNG4juFv6kpbFx4J9oQ5Hb24RSp96Dk5FudVHd6v4E=';
        $expectedParams = [
            'headers' => [
                'Accept'                        => 'application/json',
                'Accept-Language'               => 'en_US',
                'Authorization'                 => 'Bearer some-token',
            ],
        ];

        $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

        $this->assertEquals($expectedResponse, Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true));
    }

    #[Test]
    public function it_can_create_referenced_batch_payout_item(): void
    {
        $expectedResponse = $this->mockCreateReferencedBatchPayoutItemResponse();

        $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts-items';
        $expectedParams = [
            'headers' => [
                'Accept'                        => 'application/json',
                'Accept-Language'               => 'en_US',
                'Authorization'                 => 'Bearer some-token',
                'PayPal-Request-Id'             => 'some-request-id',
                'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
            ],
            'json' => $this->mockCreateReferencedBatchPayoutItemParams(),
        ];

        $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

        $this->assertEquals($expectedResponse, Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true));
    }

    #[Test]
    public function it_can_show_referenced_payout_item_details(): void
    {
        $expectedResponse = $this->mockShowReferencedBatchPayoutItemResponse();

        $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts-items/CDZEC5MJ8R5HY';
        $expectedParams = [
            'headers' => [
                'Accept'                        => 'application/json',
                'Accept-Language'               => 'en_US',
                'Authorization'                 => 'Bearer some-token',
                'PayPal-Request-Id'             => 'some-request-id',
                'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
            ],
            'json' => $this->mockCreateReferencedBatchPayoutItemParams(),
        ];

        $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

        $this->assertEquals($expectedResponse, Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true));
    }
}
