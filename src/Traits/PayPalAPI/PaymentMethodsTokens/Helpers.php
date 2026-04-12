<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI\PaymentMethodsTokens;

use Blendbyte\PayPal\Services\PayPal;
use Psr\Http\Message\StreamInterface;

trait Helpers
{
    /**
     * @var array<string, mixed>
     */
    protected $payment_source = [];

    /**
     * @var array<string, mixed>
     */
    protected $customer_source = [];

    /**
     * Set payment method token by token id.
     */
    public function setTokenSource(string $id, string $type): PayPal
    {
        $token_source = [
            'id' => $id,
            'type' => $type,
        ];

        return $this->setPaymentSourceDetails('token', $token_source);
    }

    /**
     * Set customer ID for Vault operations (list payment tokens, create token for customer).
     *
     * Alias for setCustomerSource() with a more discoverable name.
     */
    public function setCustomerId(string $id): PayPal
    {
        return $this->setCustomerSource($id);
    }

    /**
     * Set payment method token customer id.
     */
    public function setCustomerSource(string $id): PayPal
    {
        $this->customer_source = [
            'id' => $id,
        ];

        return $this;
    }

    /**
     * Set payment source data for credit card.
     *
     * @param array<string, mixed> $data
     */
    public function setPaymentSourceCard(array $data): PayPal
    {
        return $this->setPaymentSourceDetails('card', $data);
    }

    /**
     * Set payment source data for PayPal.
     *
     * @param array<string, mixed> $data
     */
    public function setPaymentSourcePayPal(array $data): PayPal
    {
        return $this->setPaymentSourceDetails('paypal', $data);
    }

    /**
     * Set payment source data for Venmo.
     *
     * @param array<string, mixed> $data
     */
    public function setPaymentSourceVenmo(array $data): PayPal
    {
        return $this->setPaymentSourceDetails('venmo', $data);
    }

    /**
     * Set payment source data for Apple Pay.
     *
     * Typically contains a `token` key with the tokenized Apple Pay payment data
     * returned by the Apple Pay JS/native SDK.
     *
     * @param array<string, mixed> $data
     */
    public function setPaymentSourceApplePay(array $data): PayPal
    {
        return $this->setPaymentSourceDetails('apple_pay', $data);
    }

    /**
     * Set payment source data for Google Pay.
     *
     * Typically contains a `decrypted_token` key with the decrypted Google Pay
     * payment data, or a `card` key for network-tokenised cards.
     *
     * @param array<string, mixed> $data
     */
    public function setPaymentSourceGooglePay(array $data): PayPal
    {
        return $this->setPaymentSourceDetails('google_pay', $data);
    }

    /**
     * Set payment source details.
     *
     * @param array<string, mixed> $data
     */
    protected function setPaymentSourceDetails(string $source, array $data): PayPal
    {
        $this->payment_source[$source] = $data;

        return $this;
    }

    /**
     * Send request for creating payment method token/source.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     */
    public function sendPaymentMethodRequest(bool $create_source = false)
    {
        $token_payload = ['payment_source' => $this->payment_source];

        if (! empty($this->customer_source)) {
            $token_payload['customer'] = $this->customer_source;
        }

        return ($create_source === true) ? $this->createPaymentSetupToken(array_filter($token_payload)) : $this->createPaymentSourceToken(array_filter($token_payload));
    }
}
