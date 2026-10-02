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

it('appeals a dispute with evidence details and files', function () {
    $this->mock->addResponse(['links' => []]);

    $evidences = [['evidence_type' => 'PROOF_OF_REFUND', 'evidence_info' => ['refund_ids' => ['R-1']]]];
    $this->client->appealDispute('PP-D-1', [$this->file], $evidences);

    $request = $this->mock->lastRequest();
    $parts = multipartParts($request);

    expect((string) $request->getUri())->toEndWith('/v1/customer/disputes/PP-D-1/appeal')
        ->and(array_keys($parts))->toBe(['input', 'sample.pdf'])
        ->and(json_decode($parts['input']['body'], true))->toBe(['evidences' => $evidences]);
});

it('appeals a dispute with files only', function () {
    $this->mock->addResponse(['links' => []]);

    $this->client->appealDispute('PP-D-1', [$this->file]);

    expect(array_keys(multipartParts($this->mock->lastRequest())))->toBe(['sample.pdf']);
});

it('provides supporting information with notes and documents', function () {
    $this->mock->addResponse(['links' => []]);

    $this->client->provideDisputeSupportingInfo('PP-D-1', 'Item was delivered on time.', [$this->file]);

    $request = $this->mock->lastRequest();
    $parts = multipartParts($request);

    expect((string) $request->getUri())->toEndWith('/v1/customer/disputes/PP-D-1/provide-supporting-info')
        ->and(array_keys($parts))->toBe(['input', 'sample.pdf'])
        ->and($parts['input']['headers'])->toContain('Content-Type: application/json')
        ->and(json_decode($parts['input']['body'], true))->toBe(['notes' => 'Item was delivered on time.']);
});

it('provides supporting information with notes only', function () {
    $this->mock->addResponse(['links' => []]);

    $this->client->provideDisputeSupportingInfo('PP-D-1', 'See attached tracking history.');

    expect(array_keys(multipartParts($this->mock->lastRequest())))->toBe(['input']);
});

it('rejects invalid dispute document types', function (string $method, array $args) {
    $invalid = [sys_get_temp_dir().'/paypal-invalid-'.uniqid().'.txt'];
    file_put_contents($invalid[0], 'not allowed');

    try {
        expect(fn () => $this->client->{$method}(...[...$args, $invalid]))->toThrow(RuntimeException::class, 'Invalid evidence file type')
            ->and($this->mock->requests())->toBe([]);
    } finally {
        unlink($invalid[0]);
    }
})->with([
    'appeal' => ['appealDispute', ['PP-D-1']],
    'supporting info' => ['provideDisputeSupportingInfo', ['PP-D-1', 'Notes']],
]);
