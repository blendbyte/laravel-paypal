<?php

namespace Srmklive\PayPal\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Srmklive\PayPal\Services\PayTheFly;

/**
 * Tests for payment link generation.
 */
class PayTheFlyPaymentTest extends TestCase
{
    private function makeClient(array $overrides = []): PayTheFly
    {
        $config = array_merge([
            'project_id'    => 'test-project-001',
            'project_key'   => 'test-secret-key',
            'private_key'   => 'ac0974bec39a17e36ba4a6b4d238ff944bacb478cbed5efcae784d7bf4f2ff80',
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

    /** @test */
    public function it_creates_bsc_payment_link(): void
    {
        if (!class_exists(\kornrunner\Keccak::class) || !class_exists(\Elliptic\EC::class)) {
            $this->markTestSkipped('kornrunner/keccak and simplito/elliptic-php required');
        }

        $client = $this->makeClient();
        $result = $client->createBscPayment('ORDER001', '0.01', 1700000000);

        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('params', $result);

        $params = $result['params'];
        $this->assertEquals(56, $params['chainId']);
        $this->assertEquals('test-project-001', $params['projectId']);
        $this->assertEquals('0.01', $params['amount']);
        $this->assertEquals('ORDER001', $params['serialNo']);
        $this->assertEquals(1700000000, $params['deadline']);
        $this->assertStringStartsWith('0x', $params['signature']);
        $this->assertEquals('0x0000000000000000000000000000000000000000', $params['token']);

        // URL should be well-formed
        $this->assertStringStartsWith('https://pro.paythefly.com/pay?', $result['url']);
        $this->assertStringContainsString('chainId=56', $result['url']);
        $this->assertStringContainsString('projectId=test-project-001', $result['url']);
    }

    /** @test */
    public function it_creates_tron_payment_link(): void
    {
        if (!class_exists(\kornrunner\Keccak::class) || !class_exists(\Elliptic\EC::class)) {
            $this->markTestSkipped('kornrunner/keccak and simplito/elliptic-php required');
        }

        $client = $this->makeClient();
        $result = $client->createTronPayment('ORDER002', '1.5', 1700000000);

        $params = $result['params'];
        $this->assertEquals(728126428, $params['chainId']);
        $this->assertEquals('T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb', $params['token']);
    }

    /** @test */
    public function it_creates_custom_token_payment(): void
    {
        if (!class_exists(\kornrunner\Keccak::class) || !class_exists(\Elliptic\EC::class)) {
            $this->markTestSkipped('kornrunner/keccak and simplito/elliptic-php required');
        }

        $client       = $this->makeClient();
        $usdtAddress  = '0x55d398326f99059fF775485246999027B3197955'; // BSC USDT
        $result       = $client->createTokenPayment('ORDER003', '100', $usdtAddress, 'bsc', 1700000000);

        $this->assertEquals($usdtAddress, $result['params']['token']);
    }

    /** @test */
    public function it_uses_default_deadline_when_not_provided(): void
    {
        if (!class_exists(\kornrunner\Keccak::class) || !class_exists(\Elliptic\EC::class)) {
            $this->markTestSkipped('kornrunner/keccak and simplito/elliptic-php required');
        }

        $client = $this->makeClient();
        $before = time() + 3600;
        $result = $client->createBscPayment('ORDER004', '0.01');
        $after  = time() + 3600;

        $deadline = $result['params']['deadline'];
        $this->assertGreaterThanOrEqual($before, $deadline);
        $this->assertLessThanOrEqual($after, $deadline);
    }

    /** @test */
    public function it_validates_serial_numbers(): void
    {
        $client = $this->makeClient();

        $this->assertTrue($client->isValidSerialNo('ORDER001'));
        $this->assertTrue($client->isValidSerialNo('order-123_test.v2'));
        $this->assertTrue($client->isValidSerialNo('a'));

        $this->assertFalse($client->isValidSerialNo(''));
        $this->assertFalse($client->isValidSerialNo('order with spaces'));
        $this->assertFalse($client->isValidSerialNo('order@#$'));
        $this->assertFalse($client->isValidSerialNo(str_repeat('a', 129)));
    }

    /** @test */
    public function it_checks_deadline_validity(): void
    {
        $client = $this->makeClient();

        $this->assertTrue($client->isDeadlineValid(time() + 3600));
        $this->assertFalse($client->isDeadlineValid(time() - 1));
    }

    /** @test */
    public function it_requires_project_id(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('project_id is required');

        $this->makeClient(['project_id' => '']);
    }

    /** @test */
    public function it_requires_contract_address_for_signing(): void
    {
        if (!class_exists(\kornrunner\Keccak::class) || !class_exists(\Elliptic\EC::class)) {
            $this->markTestSkipped('kornrunner/keccak and simplito/elliptic-php required');
        }

        $client = $this->makeClient(['contracts' => ['bsc' => '', 'tron' => '']]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Contract address not configured');

        $client->createBscPayment('ORDER005', '0.01', 1700000000);
    }
}
