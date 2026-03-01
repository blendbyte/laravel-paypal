<?php

namespace Srmklive\PayPal\Traits\PayTheFlyAPI;

use RuntimeException;

/**
 * PayTheFly Payment Link generation and payment management.
 */
trait Payments
{
    /**
     * Create a signed payment link.
     *
     * This generates a fully signed URL that redirects the payer to
     * the PayTheFly hosted payment page. The signature is generated
     * using EIP-712 typed data signing.
     *
     * @param string      $serialNo  Unique order/payment identifier
     * @param string      $amount    Human-readable amount (e.g., '0.01')
     * @param string|null $token     Token address (null = native token for active chain)
     * @param int|null    $deadline  Unix timestamp deadline (null = now + configured TTL)
     * @param string|null $chain     Chain override (null = use current chain)
     *
     * @return array{url: string, params: array}
     *
     * @throws RuntimeException
     */
    public function createPaymentLink(
        string $serialNo,
        string $amount,
        ?string $token = null,
        ?int $deadline = null,
        ?string $chain = null
    ): array {
        // Resolve chain
        $activeChain = $chain ?? $this->chain;
        if ($chain !== null) {
            $this->setChain($chain);
        }

        $chainId  = $this->getChainId();
        $decimals = $this->getDecimals();
        $token    = $token ?? $this->getNativeToken();
        $deadline = $deadline ?? (time() + ($this->config['deadline_ttl'] ?? 3600));

        // Convert to smallest unit for the signature
        $amountWei = $this->toSmallestUnit($amount, $decimals);

        // Sign the payment request
        $signature = $this->signPaymentRequest(
            $this->projectId,
            $token,
            $amountWei,
            $serialNo,
            $deadline
        );

        // Restore chain
        if ($chain !== null) {
            $this->chain = $activeChain;
        }

        // Build query parameters
        $params = [
            'chainId'   => $chainId,
            'projectId' => $this->projectId,
            'amount'    => $amount,
            'serialNo'  => $serialNo,
            'deadline'  => $deadline,
            'signature' => $signature,
            'token'     => $token,
        ];

        $baseUrl = $this->config['payment_url'] ?? 'https://pro.paythefly.com/pay';
        $url     = $baseUrl . '?' . http_build_query($params);

        return [
            'url'    => $url,
            'params' => $params,
        ];
    }

    /**
     * Create a payment link for a BSC native (BNB) payment.
     *
     * @param string   $serialNo
     * @param string   $amount
     * @param int|null $deadline
     *
     * @return array{url: string, params: array}
     */
    public function createBscPayment(string $serialNo, string $amount, ?int $deadline = null): array
    {
        return $this->createPaymentLink($serialNo, $amount, null, $deadline, 'bsc');
    }

    /**
     * Create a payment link for a TRON native (TRX) payment.
     *
     * @param string   $serialNo
     * @param string   $amount
     * @param int|null $deadline
     *
     * @return array{url: string, params: array}
     */
    public function createTronPayment(string $serialNo, string $amount, ?int $deadline = null): array
    {
        return $this->createPaymentLink($serialNo, $amount, null, $deadline, 'tron');
    }

    /**
     * Create a payment link for a specific ERC-20/TRC-20 token.
     *
     * @param string      $serialNo
     * @param string      $amount
     * @param string      $tokenAddress
     * @param string|null $chain
     * @param int|null    $deadline
     *
     * @return array{url: string, params: array}
     */
    public function createTokenPayment(
        string $serialNo,
        string $amount,
        string $tokenAddress,
        ?string $chain = null,
        ?int $deadline = null
    ): array {
        return $this->createPaymentLink($serialNo, $amount, $tokenAddress, $deadline, $chain);
    }

    /**
     * Validate a payment serial number format.
     *
     * @param string $serialNo
     *
     * @return bool
     */
    public function isValidSerialNo(string $serialNo): bool
    {
        // Serial number should be non-empty, alphanumeric with allowed separators
        return (bool) preg_match('/^[a-zA-Z0-9_\-\.]{1,128}$/', $serialNo);
    }

    /**
     * Check if a deadline timestamp is still valid.
     *
     * @param int $deadline
     *
     * @return bool
     */
    public function isDeadlineValid(int $deadline): bool
    {
        return $deadline > time();
    }
}
