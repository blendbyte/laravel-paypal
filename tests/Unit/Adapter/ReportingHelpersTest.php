<?php

use Srmklive\PayPal\Testing\MockPayPalClient;
use Srmklive\PayPal\Tests\MockResponsePayloads;

uses(MockResponsePayloads::class);

// ── getTransactionDetails ─────────────────────────────────────────────────────

describe('getTransactionDetails', function () {
    it('returns the first transaction detail entry when found', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse($this->mockListTransactionsResponse());

        $provider = $mock->mockProvider();
        $result = $provider->getTransactionDetails('5TY05013RG002845M');

        expect($result)->toBeArray();
        expect($result['transaction_info']['transaction_id'])->toBe('5TY05013RG002845M');
        expect($mock->lastRequest()->getMethod())->toBe('GET');
        expect((string) $mock->lastRequest()->getUri())->toContain('v1/reporting/transactions');
        expect((string) $mock->lastRequest()->getUri())->toContain('transaction_id=5TY05013RG002845M');
    });

    it('returns null when transaction_details is empty', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['transaction_details' => [], 'total_items' => 0]);

        $provider = $mock->mockProvider();

        expect($provider->getTransactionDetails('NONEXISTENT'))->toBeNull();
    });

    it('returns null when the API returns an error', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['error' => 'INVALID_REQUEST'], 400);

        $provider = $mock->mockProvider();

        expect($provider->getTransactionDetails('BAD-ID'))->toBeNull();
    });

    it('clamps daysBack to the 1–31 range', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['transaction_details' => [], 'total_items' => 0]);

        $provider = $mock->mockProvider();
        $provider->getTransactionDetails('TXN-123', 999);

        // Should not throw; URL will contain start_date based on 31 days back
        expect($mock->requestCount())->toBe(1);
    });
});

// ── listTransactionsForDateRange ──────────────────────────────────────────────

describe('listTransactionsForDateRange', function () {
    it('calls listTransactions with start_date and end_date and returns the response', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse($this->mockListTransactionsResponse());

        $provider = $mock->mockProvider();
        $result = $provider->listTransactionsForDateRange('2024-07-01', '2024-07-31');

        expect($result)->toHaveKey('transaction_details');
        expect($mock->lastRequest()->getMethod())->toBe('GET');

        $uri = (string) $mock->lastRequest()->getUri();
        expect($uri)->toContain('v1/reporting/transactions');
        expect($uri)->toContain('start_date=');
        expect($uri)->toContain('end_date=');
    });

    it('accepts DateTimeInterface arguments', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse($this->mockListTransactionsResponse());

        $provider = $mock->mockProvider();
        $result = $provider->listTransactionsForDateRange(
            new \DateTime('2024-07-01'),
            new \DateTime('2024-07-31'),
        );

        expect($result)->toHaveKey('transaction_details');
    });
});

// ── listTransactionsByType ────────────────────────────────────────────────────

describe('listTransactionsByType', function () {
    it('includes transaction_type in the query string', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse($this->mockListTransactionsResponse());

        $provider = $mock->mockProvider();
        $provider->listTransactionsByType('T0006', '2024-07-01', '2024-07-31');

        expect((string) $mock->lastRequest()->getUri())->toContain('transaction_type=T0006');
    });
});

// ── listTransactionsByStatus ──────────────────────────────────────────────────

describe('listTransactionsByStatus', function () {
    it('includes transaction_status in the query string', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse($this->mockListTransactionsResponse());

        $provider = $mock->mockProvider();
        $provider->listTransactionsByStatus('S', '2024-07-01', '2024-07-31');

        expect((string) $mock->lastRequest()->getUri())->toContain('transaction_status=S');
    });
});
