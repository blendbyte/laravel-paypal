<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

trait PaymentMethodsTokens
{
    use PaymentMethodsTokens\Helpers;

    /**
     * Create a payment method token.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payment-tokens/v3/#payment-tokens_create
     */
    public function createPaymentSourceToken(array $data)
    {
        $this->apiEndPoint = 'v3/vault/payment-tokens';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * List all the payment tokens.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/payment-tokens/v3/#customer_payment-tokens_get
     */
    public function listPaymentSourceTokens(int $page = 1, int $page_size = 10, bool $totals = true)
    {
        if (empty($this->customer_source['id'])) {
            throw new RuntimeException('A customer ID must be set via setCustomerId() before listing payment tokens.');
        }

        $total_required = $totals ? 'true' : 'false';
        $this->apiEndPoint = "v3/vault/payment-tokens?customer_id={$this->customer_source['id']}&page={$page}&page_size={$page_size}&total_required={$total_required}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Show details for a payment method token.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/payment-tokens/v3/#payment-tokens_get
     */
    public function showPaymentSourceTokenDetails(string $token)
    {
        $this->apiEndPoint = "v3/vault/payment-tokens/{$token}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Show details for a payment token.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/payment-tokens/v3/#payment-tokens_delete
     */
    public function deletePaymentSourceToken(string $token)
    {
        $this->apiEndPoint = "v3/vault/payment-tokens/{$token}";

        $this->verb = 'delete';

        return $this->doPayPalRequest(false);
    }

    /**
     * Create a payment setup token.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payment-tokens/v3/#setup-tokens_create
     */
    public function createPaymentSetupToken(array $data)
    {
        $this->apiEndPoint = 'v3/vault/setup-tokens';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Show details for a payment setup token.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/payment-tokens/v3/#setup-tokens_get
     */
    public function showPaymentSetupTokenDetails(string $token)
    {
        $this->apiEndPoint = "v3/vault/setup-tokens/{$token}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Delete a payment setup token.
     *
     *
     * @return array<string, mixed>|string
     *
     * @see https://developer.paypal.com/docs/api/payment-tokens/v3/#setup-tokens_delete
     */
    public function deletePaymentSetupToken(string $token)
    {
        $this->apiEndPoint = "v3/vault/setup-tokens/{$token}";

        $this->verb = 'delete';

        return $this->doPayPalRequest(false);
    }
}
