<?php

namespace Srmklive\PayPal\Testing;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Srmklive\PayPal\Services\PayPal;

class MockPayPalClient
{
    private MockHandler $handler;

    /** @var array<int, array{request: RequestInterface, response: \Psr\Http\Message\ResponseInterface}> */
    private array $history = [];

    private ?GuzzleClient $guzzle = null;

    public function __construct()
    {
        $this->handler = new MockHandler();
    }

    /**
     * Queue a response for the next HTTP call.
     *
     * Pass an array for a JSON body, or false for an empty body (e.g. 204 No Content).
     */
    public function addResponse(array|false $body = [], int $statusCode = 200): static
    {
        $this->handler->append(new Response(
            $statusCode,
            ['Content-Type' => 'application/json'],
            $body === false ? '' : Utils::jsonEncode($body),
        ));

        return $this;
    }

    /**
     * Returns a PSR-18 ClientInterface to pass to PayPal::setClient().
     */
    public function getClient(): ClientInterface
    {
        if ($this->guzzle === null) {
            $stack = HandlerStack::create($this->handler);
            $stack->push(Middleware::history($this->history));
            $this->guzzle = new GuzzleClient(['handler' => $stack]);
        }

        return $this->guzzle;
    }

    /**
     * Convenience method: wire the mock client into a PayPal provider instance
     * and pre-set a fake access token so callers skip the auth round-trip.
     */
    public function mockProvider(array $config = []): PayPal
    {
        $config = array_merge([
            'mode' => 'sandbox',
            'sandbox' => [
                'client_id' => 'mock-client-id',
                'client_secret' => 'mock-client-secret',
                'app_id' => 'APP-MOCK',
            ],
            'payment_action' => 'Sale',
            'currency' => 'USD',
            'notify_url' => '',
            'locale' => 'en_US',
            'validate_ssl' => true,
        ], $config);

        $provider = new PayPal($config);
        $provider->setAccessToken(['access_token' => 'mock-access-token', 'token_type' => 'Bearer']);
        $provider->setClient($this->getClient());

        return $provider;
    }

    /**
     * All captured PSR-7 requests, in order.
     *
     * @return RequestInterface[]
     */
    public function requests(): array
    {
        return array_map(fn ($h) => $h['request'], $this->history);
    }

    public function lastRequest(): ?RequestInterface
    {
        if (empty($this->history)) {
            return null;
        }

        return end($this->history)['request'];
    }

    public function requestCount(): int
    {
        return count($this->history);
    }
}
