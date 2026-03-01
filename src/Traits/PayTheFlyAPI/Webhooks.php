<?php

namespace Srmklive\PayPal\Traits\PayTheFlyAPI;

use RuntimeException;

/**
 * PayTheFly Webhook verification and parsing.
 *
 * Webhook body format:
 * {
 *     "data": "<json string>",
 *     "sign": "<hmac hex>",
 *     "timestamp": <unix>
 * }
 *
 * Signature: HMAC-SHA256(data + "." + timestamp, projectKey)
 *
 * The webhook handler MUST return a response body containing "success".
 */
trait Webhooks
{
    /**
     * Verify a webhook request signature.
     *
     * @param string $data      The raw JSON data string from the webhook body
     * @param string $sign      The HMAC-SHA256 hex signature
     * @param int    $timestamp The Unix timestamp
     *
     * @return bool
     *
     * @throws RuntimeException If project key is not configured
     */
    public function verifyWebhookSignature(string $data, string $sign, int $timestamp): bool
    {
        if (empty($this->projectKey)) {
            throw new RuntimeException(
                'PayTheFly project_key is required for webhook verification. Set PAYTHEFLY_PROJECT_KEY in .env'
            );
        }

        // Check timestamp tolerance (prevent replay attacks)
        $tolerance = $this->config['webhook']['tolerance'] ?? 300;
        if (abs(time() - $timestamp) > $tolerance) {
            return false;
        }

        // Compute expected signature: HMAC-SHA256(data + "." + timestamp, projectKey)
        $payload  = $data . '.' . $timestamp;
        $expected = hash_hmac('sha256', $payload, $this->projectKey);

        return hash_equals($expected, $sign);
    }

    /**
     * Parse and verify a complete webhook payload.
     *
     * @param array $payload The decoded webhook body ['data' => ..., 'sign' => ..., 'timestamp' => ...]
     *
     * @return array The parsed and verified data fields
     *
     * @throws RuntimeException If verification fails or payload is malformed
     */
    public function parseWebhook(array $payload): array
    {
        $data      = $payload['data'] ?? null;
        $sign      = $payload['sign'] ?? null;
        $timestamp = $payload['timestamp'] ?? null;

        if ($data === null || $sign === null || $timestamp === null) {
            throw new RuntimeException('Invalid webhook payload: missing required fields (data, sign, timestamp).');
        }

        if (!$this->verifyWebhookSignature($data, $sign, (int) $timestamp)) {
            throw new RuntimeException('Webhook signature verification failed.');
        }

        $decoded = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid webhook data: JSON decode failed — ' . json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * Create the "success" response body required by PayTheFly.
     *
     * @return array
     */
    public function webhookSuccessResponse(): array
    {
        return ['message' => 'success'];
    }

    /**
     * Determine the transaction type from webhook data.
     *
     * @param array $data Parsed webhook data
     *
     * @return string 'payment' or 'withdrawal'
     */
    public function getTransactionType(array $data): string
    {
        $type = $data['tx_type'] ?? 0;

        return match ((int) $type) {
            1 => 'payment',
            2 => 'withdrawal',
            default => 'unknown',
        };
    }

    /**
     * Check if a transaction is confirmed.
     *
     * @param array $data Parsed webhook data
     *
     * @return bool
     */
    public function isTransactionConfirmed(array $data): bool
    {
        return !empty($data['confirmed']);
    }

    /**
     * Extract key payment fields from webhook data.
     *
     * @param array $data Parsed webhook data
     *
     * @return array{
     *     project_id: string,
     *     chain_symbol: string,
     *     tx_hash: string,
     *     wallet: string,
     *     value: string,
     *     fee: string,
     *     serial_no: string,
     *     tx_type: string,
     *     confirmed: bool,
     *     create_at: string
     * }
     */
    public function extractPaymentData(array $data): array
    {
        return [
            'project_id'   => $data['project_id'] ?? '',
            'chain_symbol' => $data['chain_symbol'] ?? '',
            'tx_hash'      => $data['tx_hash'] ?? '',
            'wallet'       => $data['wallet'] ?? '',
            'value'        => $data['value'] ?? '0',
            'fee'          => $data['fee'] ?? '0',
            'serial_no'    => $data['serial_no'] ?? '',
            'tx_type'      => $this->getTransactionType($data),
            'confirmed'    => $this->isTransactionConfirmed($data),
            'create_at'    => $data['create_at'] ?? '',
        ];
    }
}
