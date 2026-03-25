<?php

namespace Blendbyte\PayPal\Tests\Unit\Client;

use GuzzleHttp\Utils;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class CatalogProductsTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_create_a_product(): void
    {
        $expectedResponse = $this->mockCreateCatalogProductsResponse();

        $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/catalogs/products';
        $expectedParams = [
            'headers' => [
                'Accept'            => 'application/json',
                'Accept-Language'   => 'en_US',
                'Authorization'     => 'Bearer some-token',
            ],
            'json' => $this->createProductParams(),
        ];

        $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

        $this->assertEquals($expectedResponse, Utils::jsonDecode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true));
    }

    #[Test]
    public function it_can_list_products(): void
    {
        $expectedResponse = $this->mockListCatalogProductsResponse();

        $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/catalogs/products?page=1&page_size=2&total_required=true';
        $expectedParams = [
            'headers' => [
                'Accept'            => 'application/json',
                'Accept-Language'   => 'en_US',
                'Authorization'     => 'Bearer some-token',
            ],
        ];

        $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

        $this->assertEquals($expectedResponse, Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true));
    }

    #[Test]
    public function it_can_update_a_product(): void
    {
        $expectedResponse = '';

        $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/catalogs/products/72255d4849af8ed6e0df1173';
        $expectedParams = [
            'headers' => [
                'Accept'            => 'application/json',
                'Accept-Language'   => 'en_US',
                'Authorization'     => 'Bearer some-token',
            ],
            'json' => $this->updateProductParams(),
        ];

        $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'patch');

        $this->assertEquals($expectedResponse, Utils::jsonDecode($mockHttpClient->patch($expectedEndpoint, $expectedParams)->getBody(), true));
    }

    #[Test]
    public function it_can_get_details_for_a_product(): void
    {
        $expectedResponse = $this->mockGetCatalogProductsResponse();

        $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/catalogs/products/72255d4849af8ed6e0df1173';
        $expectedParams = [
            'headers' => [
                'Accept'            => 'application/json',
                'Accept-Language'   => 'en_US',
                'Authorization'     => 'Bearer some-token',
            ],
        ];

        $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

        $this->assertEquals($expectedResponse, Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true));
    }
}
