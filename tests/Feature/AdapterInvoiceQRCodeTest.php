<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Tests\MockResponsePayloads;

uses(MockResponsePayloads::class);

function qrCodeClient(string $body): ClientInterface
{
    return new class($body) implements ClientInterface
    {
        public ?RequestInterface $lastRequest = null;

        public function __construct(private string $body) {}

        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            $this->lastRequest = $request;

            return new Response(200, ['Content-Type' => 'multipart/mixed'], $this->body);
        }
    };
}

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setAccessToken(['access_token' => 'token', 'token_type' => 'Bearer']);
});

it('sends the spec default size and action', function () {
    $http = qrCodeClient($this->mockGenerateInvoiceQRCodeResponse());
    $this->client->setClient($http);

    $this->client->generateQRCodeInvoice('INV2-1');

    expect(json_decode((string) $http->lastRequest->getBody(), true))
        ->toBe(['width' => 500, 'height' => 500, 'action' => 'pay']);
});

it('returns the base64 PNG from the multipart response', function () {
    $this->client->setClient(qrCodeClient($this->mockGenerateInvoiceQRCodeResponse()));

    $image = $this->client->generateQRCodeInvoice('INV2-1');

    expect($image)->toBeString()
        ->toStartWith('iVBORw0KGgo')
        ->toEndWith('ElFTkSuQmCC')
        ->and(base64_decode($image, true))->toStartWith("\x89PNG");
});

it('extracts the image from a multi-line multipart response', function () {
    $body = "--b1\r\nContent-Disposition: form-data; name=\"image\"\r\nContent-Type: application/octet-stream\r\n\r\niVBORw0KGgoAAAA\r\nNSUhEUg==\r\n--b1--\r\n";
    $this->client->setClient(qrCodeClient($body));

    expect($this->client->generateQRCodeInvoice('INV2-1'))->toBe('iVBORw0KGgoAAAANSUhEUg==');
});

it('returns the raw body when the response is not multipart', function () {
    $this->client->setClient(qrCodeClient('iVBORw0KGgoAAAANSUhEUg=='));

    expect($this->client->generateQRCodeInvoice('INV2-1'))->toBe('iVBORw0KGgoAAAANSUhEUg==');
});

it('rejects sizes outside 150-500 pixels', function (int $width, int $height) {
    expect(fn () => $this->client->generateQRCodeInvoice('INV2-1', $width, $height))
        ->toThrow(InvalidArgumentException::class);
})->with([[100, 300], [300, 501], [149, 149]]);

it('rejects an invalid action', function () {
    expect(fn () => $this->client->generateQRCodeInvoice('INV2-1', 300, 300, 'checkout'))
        ->toThrow(InvalidArgumentException::class);
});
