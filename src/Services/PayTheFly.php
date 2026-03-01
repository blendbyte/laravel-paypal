<?php

namespace Srmklive\PayPal\Services;

use Exception;
use RuntimeException;
use Srmklive\PayPal\Traits\PayTheFlyRequest;
use Srmklive\PayPal\Traits\PayTheFlyAPI\Payments;
use Srmklive\PayPal\Traits\PayTheFlyAPI\Signatures;
use Srmklive\PayPal\Traits\PayTheFlyAPI\Webhooks;

class PayTheFly
{
    use PayTheFlyRequest;
    use Payments;
    use Signatures;
    use Webhooks;

    /**
     * PayTheFly project ID.
     *
     * @var string
     */
    protected string $projectId;

    /**
     * Project key for webhook HMAC verification.
     *
     * @var string
     */
    protected string $projectKey;

    /**
     * ECDSA private key for EIP-712 signing.
     *
     * @var string
     */
    protected string $privateKey;

    /**
     * Active chain identifier ('bsc' or 'tron').
     *
     * @var string
     */
    protected string $chain;

    /**
     * Full configuration array.
     *
     * @var array
     */
    protected array $config;

    /**
     * PayTheFly constructor.
     *
     * @param array $config
     *
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Load and validate configuration.
     *
     * @param array $config
     *
     * @throws RuntimeException
     */
    protected function setConfig(array $config): void
    {
        $this->config = empty($config) && function_exists('config') && !empty(config('paythefly'))
            ? config('paythefly')
            : $config;

        if (empty($this->config)) {
            throw new RuntimeException(
                'PayTheFly configuration is missing. Publish the config with: php artisan vendor:publish --tag=paythefly-config'
            );
        }

        $this->projectId  = $this->config['project_id'] ?? '';
        $this->projectKey = $this->config['project_key'] ?? '';
        $this->privateKey = $this->config['private_key'] ?? '';
        $this->chain      = $this->config['default_chain'] ?? 'bsc';

        if (empty($this->projectId)) {
            throw new RuntimeException('PayTheFly project_id is required.');
        }
    }

    /**
     * Switch the active blockchain.
     *
     * @param string $chain 'bsc' or 'tron'
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function setChain(string $chain): self
    {
        $chain = strtolower($chain);

        if (!isset($this->config['chains'][$chain])) {
            throw new RuntimeException("Unsupported chain: {$chain}. Supported: bsc, tron.");
        }

        $this->chain = $chain;

        return $this;
    }

    /**
     * Get current chain identifier.
     *
     * @return string
     */
    public function getChain(): string
    {
        return $this->chain;
    }

    /**
     * Get the chain ID for the active chain.
     *
     * @return int
     */
    public function getChainId(): int
    {
        return $this->config['chains'][$this->chain]['chain_id'];
    }

    /**
     * Get the decimal precision for the active chain.
     *
     * @return int
     */
    public function getDecimals(): int
    {
        return $this->config['chains'][$this->chain]['decimals'];
    }

    /**
     * Get the native token address for the active chain.
     *
     * @return string
     */
    public function getNativeToken(): string
    {
        return $this->config['native_tokens'][$this->chain];
    }

    /**
     * Get the verifying contract address for the active chain.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getContractAddress(): string
    {
        $address = $this->config['contracts'][$this->chain] ?? '';

        if (empty($address)) {
            throw new RuntimeException("Contract address not configured for chain: {$this->chain}");
        }

        return $address;
    }

    /**
     * Get full configuration array.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
