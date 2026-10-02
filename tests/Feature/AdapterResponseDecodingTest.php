<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Srmklive\PayPal\Exceptions\PayPalApiException;
use Srmklive\PayPal\Testing\MockPayPalClient;

function rawBodyClient(string $body, int $status = 200): ClientInterface
{
    return new class($body, $status) implements ClientInterface
    {
        public function __construct(private string $body, private int $status) {}

        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            return new Response($this->status, [], $this->body);
        }
    };
}

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

it('returns an empty array for a 204 No Content response', function () {
    $this->mock->addResponse(false, 204);

    expect($this->client->deleteWebExperienceProfile('XP-1'))->toBe([]);
});

it('returns an error for a malformed JSON success response', function () {
    $this->client->setClient(rawBodyClient('<html>Bad Gateway</html>'));

    expect($this->client->showOrderDetails('O-1'))->toBe(['error' => '<html>Bad Gateway</html>']);
});

it('throws PayPalApiException for a malformed JSON success response in exception mode', function () {
    $this->client->withExceptions();
    $this->client->setClient(rawBodyClient('<html>Bad Gateway</html>'));

    expect(fn () => $this->client->showOrderDetails('O-1'))
        ->toThrow(PayPalApiException::class, '<html>Bad Gateway</html>');
});

it('decodes JSON API errors for methods that do not decode success responses', function () {
    $this->mock->addResponse(['name' => 'UNPROCESSABLE_ENTITY', 'details' => [['issue' => 'INVALID_PATCH_OPERATION']]], 422);

    $response = $this->client->updateOrder('O-1', [['op' => 'replace', 'path' => '/intent', 'value' => 'CAPTURE']]);

    expect($response['error'])->toBe(['name' => 'UNPROCESSABLE_ENTITY', 'details' => [['issue' => 'INVALID_PATCH_OPERATION']]]);
});

it('exposes decoded JSON API errors via PayPalApiException for methods that do not decode success responses', function () {
    $this->client->withExceptions();
    $this->mock->addResponse(['name' => 'RESOURCE_NOT_FOUND'], 404);

    try {
        $this->client->cancelSubscription('I-1', 'Not needed');
        $this->fail('Expected PayPalApiException');
    } catch (PayPalApiException $e) {
        expect($e->getPayPalError())->toBe(['name' => 'RESOURCE_NOT_FOUND'])
            ->and($e->getHttpStatus())->toBe(404);
    }
});

it('keeps non-JSON errors as plain strings for methods that do not decode success responses', function () {
    $this->client->setClient(rawBodyClient('Service Unavailable', 503));

    expect($this->client->updateOrder('O-1', []))->toBe(['error' => 'Service Unavailable']);
});
