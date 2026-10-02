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
