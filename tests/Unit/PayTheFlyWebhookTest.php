<?php

namespace Srmklive\PayPal\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Srmklive\PayPal\Services\PayTheFly;

/**
 * Tests for PayTheFly webhook verification and parsing.
 */
class PayTheFlyWebhookTest extends TestCase
{
    private string $projectKey = 'test-webhook-secret-key-2024';

    private function makeClient(array $overrides = []): PayTheFly
    {
        $config = array_merge([
            'project_id'    => 'test-project-001',
            'project_key'   => $this->projectKey,
            'private_key'   => '',
            'default_chain' => 'bsc',
            'contracts'     => [
                'bsc'  => '0x5FbDB2315678afecb367f032d93F642f64180aa3',
                'tron' => '0x1234567890123456789012345678901234567890',
            ],
            'native_tokens' => [
                'bsc'  => '0x0000000000000000000000000000000000000000',
                'tron' => 'T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb',
            ],
            'chains' => [
                'bsc'  => ['chain_id' => 56, 'decimals' => 18, 'symbol' => 'BSC'],
                'tron' => ['chain_id' => 728126428, 'decimals' => 6, 'symbol' => 'TRON'],
            ],
            'payment_url'  => 'https://pro.paythefly.com/pay',
            'deadline_ttl' => 3600,
            'webhook'      => ['tolerance' => 300],
        ], $overrides);

        return new PayTheFly($config);
    }

    /**
     * Build a valid webhook payload with correct HMAC signature.
     *
     * @param array    $data
     * @param int|null $timestamp
     *
     * @return array
     */
    private function buildWebhookPayload(array $data, ?int $timestamp = null): array
    {
        $timestamp = $timestamp ?? time();
        $dataJson  = json_encode($data);
        $sign      = hash_hmac('sha256', $dataJson . '.' . $timestamp, $this->projectKey);

        return [
            'data'      => $dataJson,
            'sign'      => $sign,
            'timestamp' => $timestamp,
        ];
    }

    /** @test */
    public function it_verifies_valid_webhook_signature(): void
    {
        $client = $this->makeClient();

        $data      = json_encode(['serial_no' => 'ORDER001', 'tx_type' => 1]);
        $timestamp = time();
        $sign      = hash_hmac('sha256', $data . '.' . $timestamp, $this->projectKey);

        $this->assertTrue($client->verifyWebhookSignature($data, $sign, $timestamp));
    }

    /** @test */
    public function it_rejects_invalid_webhook_signature(): void
    {
        $client = $this->makeClient();

        $data      = json_encode(['serial_no' => 'ORDER001']);
        $timestamp = time();
        $sign      = 'invalid-signature-hex-value';

        $this->assertFalse($client->verifyWebhookSignature($data, $sign, $timestamp));
    }

    /** @test */
    public function it_rejects_expired_webhook_timestamp(): void
    {
        $client = $this->makeClient();

        $data      = json_encode(['serial_no' => 'ORDER001']);
        $timestamp = time() - 600; // 10 minutes ago (exceeds 300s tolerance)
        $sign      = hash_hmac('sha256', $data . '.' . $timestamp, $this->projectKey);

        $this->assertFalse($client->verifyWebhookSignature($data, $sign, $timestamp));
    }

    /** @test */
    public function it_rejects_future_webhook_timestamp(): void
    {
        $client = $this->makeClient();

        $data      = json_encode(['serial_no' => 'ORDER001']);
        $timestamp = time() + 600; // 10 minutes in the future
        $sign      = hash_hmac('sha256', $data . '.' . $timestamp, $this->projectKey);

        $this->assertFalse($client->verifyWebhookSignature($data, $sign, $timestamp));
    }

    /** @test */
    public function it_requires_project_key_for_verification(): void
    {
        $client = $this->makeClient(['project_key' => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('project_key is required');

        $client->verifyWebhookSignature('{}', 'abc', time());
    }

    /** @test */
    public function it_parses_valid_webhook_payload(): void
    {
        $client = $this->makeClient();

        $webhookData = [
            'project_id'   => 'test-project-001',
            'chain_symbol' => 'BSC',
            'tx_hash'      => '0xabc123',
            'wallet'       => '0xdef456',
            'value'        => '0.01',
            'fee'          => '0.001',
            'serial_no'    => 'ORDER001',
            'tx_type'      => 1,
            'confirmed'    => true,
            'create_at'    => '2024-01-15 10:30:00',
        ];

        $payload = $this->buildWebhookPayload($webhookData);
        $parsed  = $client->parseWebhook($payload);

        $this->assertEquals($webhookData, $parsed);
    }

    /** @test */
    public function it_throws_on_malformed_webhook_payload(): void
    {
        $client = $this->makeClient();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing required fields');

        $client->parseWebhook(['data' => '{}']); // missing sign and timestamp
    }

    /** @test */
    public function it_throws_on_invalid_webhook_signature(): void
    {
        $client = $this->makeClient();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('verification failed');

        $client->parseWebhook([
            'data'      => '{"serial_no":"ORDER001"}',
            'sign'      => 'bad-signature',
            'timestamp' => time(),
        ]);
    }

    /** @test */
    public function it_extracts_payment_data_correctly(): void
    {
        $client = $this->makeClient();

        $data = [
            'project_id'   => 'test-project-001',
            'chain_symbol' => 'BSC',
            'tx_hash'      => '0xabc123',
            'wallet'       => '0xdef456',
            'value'        => '0.01',
            'fee'          => '0.001',
            'serial_no'    => 'ORDER001',
            'tx_type'      => 1,
            'confirmed'    => true,
            'create_at'    => '2024-01-15 10:30:00',
        ];

        $extracted = $client->extractPaymentData($data);

        $this->assertEquals('test-project-001', $extracted['project_id']);
        $this->assertEquals('BSC', $extracted['chain_symbol']);
        $this->assertEquals('ORDER001', $extracted['serial_no']);
        $this->assertEquals('payment', $extracted['tx_type']);
        $this->assertTrue($extracted['confirmed']);
    }

    /** @test */
    public function it_identifies_transaction_types(): void
    {
        $client = $this->makeClient();

        $this->assertEquals('payment', $client->getTransactionType(['tx_type' => 1]));
        $this->assertEquals('withdrawal', $client->getTransactionType(['tx_type' => 2]));
        $this->assertEquals('unknown', $client->getTransactionType(['tx_type' => 99]));
        $this->assertEquals('unknown', $client->getTransactionType([]));
    }

    /** @test */
    public function it_checks_transaction_confirmation(): void
    {
        $client = $this->makeClient();

        $this->assertTrue($client->isTransactionConfirmed(['confirmed' => true]));
        $this->assertTrue($client->isTransactionConfirmed(['confirmed' => 1]));
        $this->assertFalse($client->isTransactionConfirmed(['confirmed' => false]));
        $this->assertFalse($client->isTransactionConfirmed(['confirmed' => 0]));
        $this->assertFalse($client->isTransactionConfirmed([]));
    }

    /** @test */
    public function it_returns_success_response(): void
    {
        $client   = $this->makeClient();
        $response = $client->webhookSuccessResponse();

        $this->assertArrayHasKey('message', $response);
        $this->assertStringContainsString('success', $response['message']);
    }

    /** @test */
    public function it_prevents_timing_attacks_with_hash_equals(): void
    {
        // Verify that verifyWebhookSignature uses hash_equals (constant-time comparison)
        $sourceCode = file_get_contents(__DIR__ . '/../../src/Traits/PayTheFlyAPI/Webhooks.php');

        $this->assertStringContainsString(
            'hash_equals',
            $sourceCode,
            'Webhook verification must use hash_equals() to prevent timing attacks'
        );
    }
}
