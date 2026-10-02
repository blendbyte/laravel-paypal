<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
    $this->file = __DIR__.'/../Mocks/samples/sample.pdf';
});

/**
 * Split a multipart body into [name => [headers, body]] using the request boundary.
 *
 * @return array<string, array{headers: string, body: string}>
 */
function multipartParts(\Psr\Http\Message\RequestInterface $request): array
{
    preg_match('/boundary=(.+)$/', $request->getHeaderLine('Content-Type'), $m);
    $parts = [];

    foreach (explode('--'.$m[1], (string) $request->getBody()) as $chunk) {
        if (! str_contains($chunk, "\r\n\r\n")) {
            continue;
        }

        [$headers, $body] = explode("\r\n\r\n", ltrim($chunk, "\r\n"), 2);
        preg_match('/name="([^"]+)"/', $headers, $name);
        $parts[$name[1]] = ['headers' => $headers, 'body' => substr($body, 0, -2)];
    }

    return $parts;
}

it('sends evidence details as a JSON input part alongside the files', function () {
    $this->mock->addResponse(['links' => []]);

    $evidences = [[
        'evidence_type' => 'PROOF_OF_FULFILLMENT',
        'evidence_info' => ['tracking_info' => [['carrier_name' => 'UPS', 'tracking_number' => '1Z999']]],
        'notes' => 'Delivered.',
    ]];

    $this->client->provideDisputeEvidence('PP-D-1', [$this->file], $evidences);

    $parts = multipartParts($this->mock->lastRequest());

    expect(array_keys($parts))->toBe(['input', 'sample.pdf'])
        ->and($parts['input']['headers'])->toContain('Content-Type: application/json')
        ->and(json_decode($parts['input']['body'], true))->toBe(['evidences' => $evidences])
        ->and($parts['sample.pdf']['body'])->toBe(file_get_contents($this->file));
});

it('sends only the files when no evidence details are given', function () {
    $this->mock->addResponse(['links' => []]);

    $this->client->provideDisputeEvidence('PP-D-1', [$this->file]);

    expect(array_keys(multipartParts($this->mock->lastRequest())))->toBe(['sample.pdf']);
});
