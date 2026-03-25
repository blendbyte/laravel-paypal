<?php

namespace Blendbyte\PayPal\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Services\PayPal as PayPalClient;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class AdapterTest extends TestCase
{
    use MockClientClasses;
    use MockResponsePayloads;

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $client = new PayPalClient($this->getMockCredentials());

        $this->assertInstanceOf(PayPalClient::class, $client);
    }

    #[Test]
    public function it_throws_exception_if_invalid_credentials_are_provided(): void
    {
        $this->expectException(\RuntimeException::class);

        $client = new PayPalClient();
    }

    #[Test]
    public function it_throws_exception_if_invalid_mode_is_provided(): void
    {
        $this->expectException(\RuntimeException::class);
        // $this->expectErrorMessage('Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');

        $credentials = $this->getMockCredentials();
        $credentials['mode'] = '';

        $client = new PayPalClient($credentials);
    }

    #[Test]
    public function it_throws_exception_if_empty_credentials_are_provided(): void
    {
        $this->expectException(\RuntimeException::class);
        // $this->expectErrorMessage('Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');

        $credentials = $this->getMockCredentials();
        $credentials['sandbox'] = [];

        $client = new PayPalClient($credentials);
    }

    #[Test]
    public function it_throws_exception_if_credentials_items_are_not_provided(): void
    {
        $item = 'client_id';

        $this->expectException(\RuntimeException::class);
        // $this->expectErrorMessage("{$item} missing from the provided configuration. Please add your application {$item}.");

        $credentials = $this->getMockCredentials();
        $credentials['sandbox'][$item] = '';

        $client = new PayPalClient($credentials);
    }

    #[Test]
    public function it_can_get_access_token(): void
    {
        $expectedResponse = $this->mockAccessTokenResponse();

        $expectedMethod = 'getAccessToken';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, false);

        $mockClient->setApiCredentials($this->getMockCredentials());

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }
}
