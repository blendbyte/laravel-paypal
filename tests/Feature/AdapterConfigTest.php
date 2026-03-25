<?php

namespace Blendbyte\PayPal\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Services\PayPal as PayPalClient;
use Blendbyte\PayPal\Tests\MockClientClasses;

class AdapterConfigTest extends TestCase
{
    use MockClientClasses;

    /** @var PayPalClient */
    protected PayPalClient $client;

    protected function setUp(): void
    {
        $this->client = new PayPalClient($this->getApiCredentials());

        parent::setUp();
    }

    #[Test]
    public function it_throws_exception_if_invalid_credentials_are_provided(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');

        $this->client = new PayPalClient([]);
    }

    #[Test]
    public function it_throws_exception_if_invalid_mode_is_provided(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');

        $credentials = $this->getApiCredentials();
        $credentials['mode'] = '';

        $this->client = new PayPalClient($credentials);
    }

    #[Test]
    public function it_throws_exception_if_empty_credentials_are_provided(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');

        $credentials = $this->getApiCredentials();
        $credentials['sandbox'] = [];

        $this->client = new PayPalClient($credentials);
    }

    #[Test]
    public function it_throws_exception_if_credentials_items_are_not_provided(): void
    {
        $item = 'client_id';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("{$item} missing from the provided configuration. Please add your application {$item}.");

        $credentials = $this->getApiCredentials();
        $credentials['sandbox'][$item] = '';

        $client = new PayPalClient($credentials);
    }

    #[Test]
    public function it_can_take_valid_credentials_and_return_the_client_instance(): void
    {
        $this->assertInstanceOf(PayPalClient::class, $this->client);
    }

    #[Test]
    public function it_throws_exception_if_invalid_credentials_are_provided_through_method(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->client->setApiCredentials([]);
    }

    #[Test]
    public function it_returns_the_client_instance_if_valid_credentials_are_provided_through_method(): void
    {
        $this->client->setApiCredentials($this->getApiCredentials());

        $this->assertInstanceOf(PayPalClient::class, $this->client);
    }

    #[Test]
    public function it_throws_exception_if_invalid_currency_is_set(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->client->setCurrency('PKR');

        $this->assertNotEquals('PKR', $this->client->getCurrency());
    }

    #[Test]
    public function it_can_set_a_valid_currency(): void
    {
        $this->client->setCurrency('EUR');

        $this->assertNotEmpty($this->client->getCurrency());
        $this->assertEquals('EUR', $this->client->getCurrency());
    }

    #[Test]
    public function it_can_set_a_request_header(): void
    {
        $this->client->setRequestHeader('Prefer', 'return=representation');

        $this->assertNotEmpty($this->client->getRequestHeader('Prefer'));
        $this->assertEquals($this->client->getRequestHeader('Prefer'), 'return=representation');
    }

    #[Test]
    public function it_can_set_multiple_request_headers(): void
    {
        $this->client->setRequestHeaders([
            'PayPal-Request-Id'             => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ]);

        $this->assertNotEmpty($this->client->getRequestHeader('PayPal-Request-Id'));
        $this->assertEquals($this->client->getRequestHeader('PayPal-Partner-Attribution-Id'), 'some-attribution-id');
    }

    #[Test]
    public function it_throws_exception_if_options_header_not_set(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode('0');
        $this->expectExceptionMessage('Options header is not set.');

        $this->client->getRequestHeader('Prefer');
    }
}
