<?php

namespace Srmklive\PayPal\Traits\PayTheFlyAPI;

use RuntimeException;

/**
 * EIP-712 Typed Data Signing for PayTheFly payment links.
 *
 * CRITICAL: This implementation uses kornrunner/keccak for Keccak-256 hashing.
 * PHP's native hash('sha3-256', ...) is NOT the same as Keccak-256.
 * If the kornrunner/keccak package is missing, this will throw an exception.
 *
 * @see https://eips.ethereum.org/EIPS/eip-712
 */
trait Signatures
{
    /**
     * EIP-712 Domain name.
     */
    private const EIP712_DOMAIN_NAME = 'PayTheFlyPro';

    /**
     * EIP-712 Domain version.
     */
    private const EIP712_DOMAIN_VERSION = '1';

    /**
     * EIP-712 Domain type hash.
     * keccak256("EIP712Domain(string name,string version,uint256 chainId,address verifyingContract)")
     */
    private ?string $domainTypeHash = null;

    /**
     * EIP-712 PaymentRequest type hash.
     * keccak256("PaymentRequest(string projectId,address token,uint256 amount,string serialNo,uint256 deadline)")
     */
    private ?string $paymentRequestTypeHash = null;

    /**
     * Compute Keccak-256 hash of the given data.
     *
     * IMPORTANT: Uses kornrunner/keccak exclusively.
     * DO NOT fall back to hash('sha3-256', ...) — it produces different output.
     *
     * @param string $data Raw binary data to hash
     *
     * @return string Raw 32-byte hash
     *
     * @throws RuntimeException If kornrunner/keccak is not installed
     */
    protected function keccak256(string $data): string
    {
        if (!class_exists(\kornrunner\Keccak::class)) {
            throw new RuntimeException(
                'The kornrunner/keccak package is required for EIP-712 signing. '
                . 'Install it with: composer require kornrunner/keccak. '
                . 'DO NOT use PHP\'s hash("sha3-256") as a substitute — it is NOT Keccak-256.'
            );
        }

        return hex2bin(\kornrunner\Keccak::hash($data, 256));
    }

    /**
     * Get the EIP-712 domain type hash.
     *
     * @return string Raw 32 bytes
     */
    protected function getDomainTypeHash(): string
    {
        if ($this->domainTypeHash === null) {
            $this->domainTypeHash = $this->keccak256(
                'EIP712Domain(string name,string version,uint256 chainId,address verifyingContract)'
            );
        }

        return $this->domainTypeHash;
    }

    /**
     * Get the PaymentRequest type hash.
     *
     * @return string Raw 32 bytes
     */
    protected function getPaymentRequestTypeHash(): string
    {
        if ($this->paymentRequestTypeHash === null) {
            $this->paymentRequestTypeHash = $this->keccak256(
                'PaymentRequest(string projectId,address token,uint256 amount,string serialNo,uint256 deadline)'
            );
        }

        return $this->paymentRequestTypeHash;
    }

    /**
     * Encode an EIP-712 domain separator.
     *
     * @param int    $chainId
     * @param string $verifyingContract Hex address with 0x prefix
     *
     * @return string Raw 32-byte domain separator hash
     */
    protected function encodeDomainSeparator(int $chainId, string $verifyingContract): string
    {
        $encoded = $this->getDomainTypeHash()
            . $this->keccak256(self::EIP712_DOMAIN_NAME)
            . $this->keccak256(self::EIP712_DOMAIN_VERSION)
            . $this->abiEncodeUint256($chainId)
            . $this->abiEncodeAddress($verifyingContract);

        return $this->keccak256($encoded);
    }

    /**
     * Encode the PaymentRequest struct hash.
     *
     * @param string $projectId
     * @param string $token     Token address (hex with 0x prefix)
     * @param string $amount    Amount as uint256 (decimal string, already in smallest unit)
     * @param string $serialNo
     * @param int    $deadline  Unix timestamp
     *
     * @return string Raw 32-byte struct hash
     */
    protected function encodePaymentRequest(
        string $projectId,
        string $token,
        string $amount,
        string $serialNo,
        int $deadline
    ): string {
        $encoded = $this->getPaymentRequestTypeHash()
            . $this->keccak256($projectId)
            . $this->abiEncodeAddress($token)
            . $this->abiEncodeUint256($amount)
            . $this->keccak256($serialNo)
            . $this->abiEncodeUint256($deadline);

        return $this->keccak256($encoded);
    }

    /**
     * Build the full EIP-712 digest to sign.
     *
     * digest = keccak256("\x19\x01" || domainSeparator || structHash)
     *
     * @param string $domainSeparator Raw 32 bytes
     * @param string $structHash      Raw 32 bytes
     *
     * @return string Raw 32-byte digest
     */
    protected function buildEIP712Digest(string $domainSeparator, string $structHash): string
    {
        return $this->keccak256("\x19\x01" . $domainSeparator . $structHash);
    }

    /**
     * Sign an EIP-712 payment request and return the signature.
     *
     * @param string      $projectId
     * @param string      $token      Token address
     * @param string      $amount     Amount in smallest unit (wei/sun)
     * @param string      $serialNo   Unique order serial number
     * @param int         $deadline   Unix timestamp
     * @param string|null $chain      Chain name override (null = use current)
     *
     * @return string 0x-prefixed hex signature (65 bytes: r + s + v)
     *
     * @throws RuntimeException
     */
    public function signPaymentRequest(
        string $projectId,
        string $token,
        string $amount,
        string $serialNo,
        int $deadline,
        ?string $chain = null
    ): string {
        if (empty($this->privateKey)) {
            throw new RuntimeException('Private key is required for EIP-712 signing. Set PAYTHEFLY_PRIVATE_KEY in .env');
        }

        if (!class_exists(\Elliptic\EC::class)) {
            throw new RuntimeException(
                'The simplito/elliptic-php package is required for ECDSA signing. '
                . 'Install with: composer require simplito/elliptic-php'
            );
        }

        // Resolve chain parameters
        $originalChain = $this->chain;
        if ($chain !== null) {
            $this->setChain($chain);
        }

        $chainId           = $this->getChainId();
        $verifyingContract = $this->getContractAddress();

        // Restore chain if we temporarily switched
        if ($chain !== null) {
            $this->chain = $originalChain;
        }

        // Build EIP-712 digest
        $domainSeparator = $this->encodeDomainSeparator($chainId, $verifyingContract);
        $structHash      = $this->encodePaymentRequest($projectId, $token, $amount, $serialNo, $deadline);
        $digest          = $this->buildEIP712Digest($domainSeparator, $structHash);

        // Sign with secp256k1
        $ec        = new \Elliptic\EC('secp256k1');
        $key       = $ec->keyFromPrivate(ltrim($this->privateKey, '0x'));
        $signature = $key->sign(bin2hex($digest), ['canonical' => true]);

        // Encode as Ethereum signature (r || s || v)
        $r = str_pad($signature->r->toString(16), 64, '0', STR_PAD_LEFT);
        $s = str_pad($signature->s->toString(16), 64, '0', STR_PAD_LEFT);
        $v = dechex($signature->recoveryParam + 27);

        return '0x' . $r . $s . $v;
    }

    /**
     * ABI-encode a uint256 value as 32 bytes.
     *
     * @param int|string $value
     *
     * @return string Raw 32 bytes
     */
    protected function abiEncodeUint256($value): string
    {
        if (is_int($value)) {
            $hex = dechex($value);
        } else {
            // Handle large decimal strings using bcmath
            $hex = $this->decimalToHex((string) $value);
        }

        return hex2bin(str_pad($hex, 64, '0', STR_PAD_LEFT));
    }

    /**
     * ABI-encode an Ethereum address as 32 bytes (left-padded).
     *
     * @param string $address 0x-prefixed hex address
     *
     * @return string Raw 32 bytes
     */
    protected function abiEncodeAddress(string $address): string
    {
        $clean = strtolower(ltrim($address, '0x'));

        return hex2bin(str_pad($clean, 64, '0', STR_PAD_LEFT));
    }

    /**
     * Convert a decimal string to a hex string (for large numbers).
     *
     * @param string $decimal
     *
     * @return string Hex string without 0x prefix
     */
    protected function decimalToHex(string $decimal): string
    {
        if ($decimal === '0') {
            return '0';
        }

        if (!function_exists('bcmod') || !function_exists('bcdiv')) {
            throw new RuntimeException('bcmath extension is required for handling large uint256 values.');
        }

        $hex = '';
        while (bccomp($decimal, '0') > 0) {
            $remainder = bcmod($decimal, '16');
            $hex       = dechex((int) $remainder) . $hex;
            $decimal   = bcdiv($decimal, '16', 0);
        }

        return $hex;
    }

    /**
     * Convert a human-readable amount to the smallest unit based on decimals.
     *
     * e.g., toSmallestUnit('0.01', 18) => '10000000000000000'
     *
     * @param string $amount   Human-readable amount (e.g., '0.01')
     * @param int    $decimals Number of decimal places
     *
     * @return string Amount in smallest unit
     */
    public function toSmallestUnit(string $amount, int $decimals): string
    {
        if (!function_exists('bcmul') || !function_exists('bcpow')) {
            throw new RuntimeException('bcmath extension is required for precise decimal arithmetic.');
        }

        $multiplier = bcpow('10', (string) $decimals, 0);

        return bcmul($amount, $multiplier, 0);
    }

    /**
     * Convert an amount in smallest unit to human-readable format.
     *
     * @param string $amount   Amount in smallest unit
     * @param int    $decimals Number of decimal places
     *
     * @return string Human-readable amount
     */
    public function fromSmallestUnit(string $amount, int $decimals): string
    {
        if (!function_exists('bcdiv') || !function_exists('bcpow')) {
            throw new RuntimeException('bcmath extension is required for precise decimal arithmetic.');
        }

        $divisor = bcpow('10', (string) $decimals, 0);

        return bcdiv($amount, $divisor, $decimals);
    }
}
