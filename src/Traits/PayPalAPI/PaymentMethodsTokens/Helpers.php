<?php

namespace Srmklive\PayPal\Traits\PayPalAPI\PaymentMethodsTokens;

use Psr\Http\Message\StreamInterface;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

trait Helpers
{
    /**
     * @var array
     */
    protected array $payment_source = [];

    /**
     * @var array
     */
    protected array $customer_source = [];

    /**
     * Set payment method token by token id.
     *
     * @param string $id
     * @param string $type
     *
     * @return PayPalClient
     */
    public function setTokenSource(string $id, string $type): PayPalClient
    {
        $token_source = [
            'id'    => $id,
            'type'  => $type,
        ];

        return $this->setPaymentSourceDetails('token', $token_source);
    }

    /**
     * Set payment method token customer id.
     *
     * @param string $id
     *
     * @return PayPalClient
     */
    public function setCustomerSource(string $id): PayPalClient
    {
        $this->customer_source = [
            'id' => $id,
        ];

        return $this;
    }

    /**
     * Set payment source data for credit card.
     *
     * @param array $data
     *
     * @return PayPalClient
     */
    public function setPaymentSourceCard(array $data): PayPalClient
    {
        return $this->setPaymentSourceDetails('card', $data);
    }

    /**
     * Set payment source data for PayPal.
     *
     * @param array $data
     *
     * @return PayPalClient
     */
    public function setPaymentSourcePayPal(array $data): PayPalClient
    {
        return $this->setPaymentSourceDetails('paypal', $data);
    }

    /**
     * Set payment source data for Venmo.
     *
     * @param array $data
     *
     * @return PayPalClient
     */
    public function setPaymentSourceVenmo(array $data): PayPalClient
    {
        return $this->setPaymentSourceDetails('venmo', $data);
    }

    /**
     * Set payment source details.
     *
     * @param string $source
     * @param array  $data
     *
     * @return PayPalClient
     */
    protected function setPaymentSourceDetails(string $source, array $data): PayPalClient
    {
        $this->payment_source[$source] = $data;

        return $this;
    }

    /**
     * Send request for creating payment method token/source.
     *
     * @param bool $create_source
     *
     * @throws \Throwable
     *
     * @return array|StreamInterface|string
     */
    public function sendPaymentMethodRequest(bool $create_source = false): StreamInterface|array|string
    {
        $token_payload = ['payment_source' => $this->payment_source];

        if (!empty($this->customer_source)) {
            $token_payload['customer'] = $this->customer_source;
        }

        return ($create_source === true) ? $this->createPaymentSetupToken(array_filter($token_payload)) : $this->createPaymentSourceToken(array_filter($token_payload));
    }
}
