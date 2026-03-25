<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

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

        $expectedParams = $this->createProductParams();

        $expectedMethod = 'createProduct';
        $additionalMethod = 'setRequestHeader';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();
        $mockClient->{$additionalMethod}('PayPal-Request-Id', 'some-request-id');

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_list_products(): void
    {
        $expectedResponse = $this->mockListCatalogProductsResponse();

        $expectedMethod = 'listProducts';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }

    #[Test]
    public function it_can_update_a_product(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->updateProductParams();

        $expectedMethod = 'updateProduct';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('72255d4849af8ed6e0df1173', $expectedParams));
    }

    #[Test]
    public function it_can_get_details_for_a_product(): void
    {
        $expectedResponse = $this->mockGetCatalogProductsResponse();

        $expectedMethod = 'showProductDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('72255d4849af8ed6e0df1173'));
    }
}
