<?php

namespace Srmklive\PayPal\Tests;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler as HttpMockHandler;
use GuzzleHttp\HandlerStack as HttpHandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as HttpResponse;
use GuzzleHttp\Psr7\Stream as HttpStream;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;

trait MockClientClasses
{
    private function mock_http_client($response): HttpClient
    {
        $mock = new HttpMockHandler([
            new HttpResponse(
                200,
                [],
                ($response === false) ? '' : Utils::jsonEncode($response)
            ),
        ]);

        $handler = HttpHandlerStack::create($mock);

        return new HttpClient(['handler' => $handler]);
    }

    /**
     * Like mock_http_client() but also captures every PSR-7 transaction into
     * $container so tests can assert on the exact request that was sent.
     *
     * Usage:
     *   $container = [];
     *   $this->client->setClient($this->mock_http_client_capturing($response, $container));
     *   $this->client->someApiCall();
     *   $request = $container[0]['request']; // PSR-7 RequestInterface
     *
     * @param  mixed                                             $response  Same as mock_http_client().
     * @param  array<int, array{request: mixed, response: mixed}>  $container Populated after the call.
     */
    private function mock_http_client_capturing($response, array &$container): HttpClient
    {
        $mock = new HttpMockHandler([
            new HttpResponse(
                200,
                [],
                ($response === false) ? '' : Utils::jsonEncode($response)
            ),
        ]);

        $stack = HttpHandlerStack::create($mock);
        $stack->push(Middleware::history($container));

        return new HttpClient(['handler' => $stack]);
    }

    private function mock_http_request($expectedResponse, $expectedEndpoint, $expectedParams, $expectedMethod = 'post')
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->exactly(1))
            ->method('getBody')
            ->willReturn(new HttpStream(fopen('data://text/plain,'.$expectedResponse, 'r')));

        $mockHttpClient = $this->createPartialMock(HttpClient::class, [$expectedMethod]);
        $mockHttpClient->expects($this->once())
            ->method($expectedMethod)
            ->with($expectedEndpoint, $expectedParams)
            ->willReturn($mockResponse);

        return $mockHttpClient;
    }

    private function mock_client($expectedResponse, $expectedMethod, $token = false, $additionalMethod = null)
    {
        $methods = [$expectedMethod, 'setApiCredentials'];
        $methods[] = ($token) ? 'getAccessToken' : '';
        $methods[] = isset($additionalMethod) ? $additionalMethod : '';

        $mockClient = $this->createPartialMock(PayPalClient::class, array_filter($methods));

        if ($token) {
            $mockClient->expects($this->exactly(1))
                ->method('getAccessToken');
        }

        if (isset($additionalMethod)) {
            $mockClient->expects($this->any())
                ->method($additionalMethod);
        }

        $mockClient->expects($this->exactly(1))
            ->method('setApiCredentials');

        $mockClient->expects($this->exactly(1))
            ->method($expectedMethod)
            ->willReturn($expectedResponse);

        return $mockClient;
    }

    private function getMockCredentials(): array
    {
        return [
            'mode' => 'sandbox',
            'sandbox' => [
                'client_id' => 'some-client-id',
                'client_secret' => 'some-access-token',
                'app_id' => 'some-app-id',
            ],
            'payment_action' => 'Sale',
            'currency' => 'USD',
            'notify_url' => '',
            'locale' => 'en_US',
            'validate_ssl' => true,
        ];
    }

    private function getApiCredentials(): array
    {
        return [
            'mode' => 'sandbox',
            'sandbox' => [
                'client_id' => 'AbJgVQM6g57qPrXimGkBz1UaBOXn1dKLSdUj7BgiB3JhzJRCapzCnkPq6ycOOmgXHtnDZcjwLMJ2IdAI',
                'client_secret' => 'EPd_XBNkfhU3-MlSw6gpa6EJj9x8QBdsC3o77jZZWjcFy_hrjR4kzBP8QN3MPPH4g52U_acG4-ogWUxI',
                'app_id' => 'APP-80W284485P519543T',
            ],
            'payment_action' => 'Sale',
            'currency' => 'USD',
            'notify_url' => '',
            'locale' => 'en_US',
            'validate_ssl' => true,
        ];
    }
}
