<?php

namespace Srmklive\PayPal\Testing;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Srmklive\PayPal\Services\PayPal;

class MockPayPalClient implements ClientInterface
{
    /** @var ResponseInterface[] */
    private array $responseQueue = [];

    /** @var RequestInterface[] */
    private array $history = [];

    /**
     * Queue a response for the next HTTP call.
     *
     * Pass an array for a JSON body, or false for an empty body (e.g. 204 No Content).
     *
     * @param array<string, mixed>|false $body
     */
    public function addResponse(array|false $body = [], int $statusCode = 200): static
    {
        $this->responseQueue[] = new Response(
            $statusCode,
            ['Content-Type' => 'application/json'],
            $body === false ? '' : Utils::jsonEncode($body),
        );

        return $this;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->history[] = $request;

        if ($this->responseQueue === []) {
            throw new \UnderflowException('MockPayPalClient has no more queued responses.');
        }

        return array_shift($this->responseQueue);
    }

    /**
     * Convenience method: wire this mock into a PayPal provider instance
     * and pre-set a fake access token so callers skip the auth round-trip.
     *
     * @param array<string, mixed> $config
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
        $provider->setClient($this);

        return $provider;
    }

    /**
     * All captured PSR-7 requests, in order.
     *
     * @return RequestInterface[]
     */
    public function requests(): array
    {
        return $this->history;
    }

    public function lastRequest(): ?RequestInterface
    {
        if ($this->history === []) {
            return null;
        }

        return end($this->history);
    }

    public function requestCount(): int
    {
        return count($this->history);
    }
}
