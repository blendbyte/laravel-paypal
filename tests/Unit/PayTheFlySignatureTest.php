<?php

namespace Srmklive\PayPal\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Srmklive\PayPal\Services\PayTheFly;

/**
 * Tests for EIP-712 signature generation and Keccak-256 hashing.
 */
class PayTheFlySignatureTest extends TestCase
{
    private function makeClient(array $overrides = []): PayTheFly
    {
        $config = array_merge([
            'project_id'    => 'test-project-001',
            'project_key'   => 'test-secret-key',
            'private_key'   => 'ac0974bec39a17e36ba4a6b4d238ff944bacb478cbed5efcae784d7bf4f2ff80', // Hardhat #0
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
                'bsc' => [
                    'chain_id' => 56,
                    'decimals' => 18,
                    'symbol'   => 'BSC',
                ],
                'tron' => [
                    'chain_id' => 728126428,
                    'decimals' => 6,
                    'symbol'   => 'TRON',
                ],
            ],
            'payment_url'  => 'https://pro.paythefly.com/pay',
            'deadline_ttl' => 3600,
            'webhook'      => [
                'tolerance' => 300,
            ],
        ], $overrides);

        return new PayTheFly($config);
    }

    /** @test */
    public function it_requires_kornrunner_keccak_package(): void
    {
        // This test verifies the code checks for the Keccak class
        // If kornrunner/keccak IS installed, the sign call should succeed
        // If it's NOT installed, it should throw RuntimeException (not silently use sha3-256)
        $client = $this->makeClient();

        if (!class_exists(\kornrunner\Keccak::class)) {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('kornrunner/keccak');
        }

        $signature = $client->signPaymentRequest(
            'test-project-001',
            '0x0000000000000000000000000000000000000000',
            '10000000000000000',
            'ORDER001',
            time() + 3600
        );

        if (class_exists(\kornrunner\Keccak::class)) {
            $this->assertStringStartsWith('0x', $signature);
            $this->assertEquals(132, strlen($signature)); // 0x + 64(r) + 64(s) + 2(v)
        }
    }

    /** @test */
    public function it_never_uses_sha3_256_as_fallback(): void
    {
        // CRITICAL: Verify that NO code path uses hash('sha3-256', ...)
        // sha3-256 (FIPS 202) != Keccak-256 (pre-FIPS, used by Ethereum)
        $signatureFile = file_get_contents(__DIR__ . '/../../src/Traits/PayTheFlyAPI/Signatures.php');

        $this->assertStringNotContainsString(
            "hash('sha3-256'",
            $signatureFile,
            'CRITICAL: sha3-256 must NEVER be used as a Keccak-256 substitute'
        );

        $this->assertStringNotContainsString(
            'hash("sha3-256"',
            $signatureFile,
            'CRITICAL: sha3-256 must NEVER be used as a Keccak-256 substitute'
        );
    }

    /** @test */
    public function it_converts_amounts_to_smallest_unit(): void
    {
        $client = $this->makeClient();

        // BSC: 18 decimals
        $this->assertEquals('10000000000000000', $client->toSmallestUnit('0.01', 18));
        $this->assertEquals('1000000000000000000', $client->toSmallestUnit('1', 18));
        $this->assertEquals('1500000000000000000', $client->toSmallestUnit('1.5', 18));

        // TRON: 6 decimals
        $this->assertEquals('10000', $client->toSmallestUnit('0.01', 6));
        $this->assertEquals('1000000', $client->toSmallestUnit('1', 6));
    }

    /** @test */
    public function it_converts_amounts_from_smallest_unit(): void
    {
        $client = $this->makeClient();

        $this->assertEquals('0.010000000000000000', $client->fromSmallestUnit('10000000000000000', 18));
        $this->assertEquals('1.000000000000000000', $client->fromSmallestUnit('1000000000000000000', 18));
        $this->assertEquals('0.010000', $client->fromSmallestUnit('10000', 6));
    }

    /** @test */
    public function it_rejects_missing_private_key(): void
    {
        $client = $this->makeClient(['private_key' => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Private key is required');

        $client->signPaymentRequest(
            'test-project-001',
            '0x0000000000000000000000000000000000000000',
            '10000000000000000',
            'ORDER001',
            time() + 3600
        );
    }

    /** @test */
    public function it_produces_deterministic_signatures(): void
    {
        if (!class_exists(\kornrunner\Keccak::class) || !class_exists(\Elliptic\EC::class)) {
            $this->markTestSkipped('kornrunner/keccak and simplito/elliptic-php required');
        }

        $client   = $this->makeClient();
        $deadline = 1700000000; // Fixed timestamp for determinism

        $sig1 = $client->signPaymentRequest(
            'test-project-001',
            '0x0000000000000000000000000000000000000000',
            '10000000000000000',
            'ORDER001',
            $deadline
        );

        $sig2 = $client->signPaymentRequest(
            'test-project-001',
            '0x0000000000000000000000000000000000000000',
            '10000000000000000',
            'ORDER001',
            $deadline
        );

        $this->assertEquals($sig1, $sig2, 'Signatures should be deterministic for the same input');
    }

    /** @test */
    public function it_produces_different_signatures_for_different_inputs(): void
    {
        if (!class_exists(\kornrunner\Keccak::class) || !class_exists(\Elliptic\EC::class)) {
            $this->markTestSkipped('kornrunner/keccak and simplito/elliptic-php required');
        }

        $client   = $this->makeClient();
        $deadline = 1700000000;

        $sig1 = $client->signPaymentRequest(
            'test-project-001',
            '0x0000000000000000000000000000000000000000',
            '10000000000000000',
            'ORDER001',
            $deadline
        );

        $sig2 = $client->signPaymentRequest(
            'test-project-001',
            '0x0000000000000000000000000000000000000000',
            '10000000000000000',
            'ORDER002', // Different serial
            $deadline
        );

        $this->assertNotEquals($sig1, $sig2);
    }

    /** @test */
    public function it_switches_chains_correctly(): void
    {
        $client = $this->makeClient();

        $this->assertEquals('bsc', $client->getChain());
        $this->assertEquals(56, $client->getChainId());
        $this->assertEquals(18, $client->getDecimals());

        $client->setChain('tron');

        $this->assertEquals('tron', $client->getChain());
        $this->assertEquals(728126428, $client->getChainId());
        $this->assertEquals(6, $client->getDecimals());
    }

    /** @test */
    public function it_rejects_unsupported_chains(): void
    {
        $client = $this->makeClient();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported chain');

        $client->setChain('ethereum');
    }

    /** @test */
    public function it_returns_correct_native_tokens(): void
    {
        $client = $this->makeClient();

        $this->assertEquals(
            '0x0000000000000000000000000000000000000000',
            $client->getNativeToken()
        );

        $client->setChain('tron');
        $this->assertEquals(
            'T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb',
            $client->getNativeToken()
        );
    }
}
