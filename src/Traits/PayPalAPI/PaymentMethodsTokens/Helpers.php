<?php

namespace Srmklive\PayPal\Traits\PayPalAPI\PaymentMethodsTokens;

use Srmklive\PayPal\Services\PayPal;
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
     * Return the current payment_source array.
     *
     * Useful for inspecting what has been built before passing it to
     * createOrder() or createOrderWithPaymentSource().
     *
     * @return array<string, mixed>
     */
    public function getPaymentSource(): array
    {
        return $this->payment_source;
    }

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
     * Set payment source data for Pay Upon Invoice (Rechnungskauf).
     *
     * Available for DE/AT merchants only. The $data array must include the
     * buyer's name, email, birth_date, phone, and billing_address. An
     * experience_context (locale, return_url, cancel_url) is recommended.
     *
     * @param array<string, mixed> $data
     *
     * @see https://developer.paypal.com/docs/checkout/pay-upon-invoice/
     */
    public function setPaymentSourcePayUponInvoice(array $data): PayPal
    {
        return $this->setPaymentSourceDetails('pay_upon_invoice', $data);
    }

    /**
     * Set billing address for the card payment source (ACDC / unbranded card).
     *
     * Merges into any existing payment_source.card data set by setPaymentSourceCard().
     * Pass an empty string for $address_line_2 to omit it.
     */
    public function setCardBillingAddress(
        string $address_line_1,
        string $admin_area_2,
        string $admin_area_1,
        string $postal_code,
        string $country_code,
        string $address_line_2 = ''
    ): PayPal {
        $address = array_filter([
            'address_line_1' => $address_line_1,
            'address_line_2' => $address_line_2,
            'admin_area_2' => $admin_area_2,
            'admin_area_1' => $admin_area_1,
            'postal_code' => $postal_code,
            'country_code' => $country_code,
        ]);

        return $this->mergeCardDetails('billing_address', $address);
    }

    /**
     * Configure card vaulting for an ACDC order.
     *
     * Call before createOrderWithPaymentSource() to instruct PayPal to vault the
     * card on a successful payment. Use 'ON_SUCCESS' (default) or 'ON_CAPTURE'.
     *
     * Can be combined with setCardVerification() — both merge into
     * payment_source.card.attributes without overwriting each other.
     *
     * @see https://developer.paypal.com/docs/checkout/advanced/customize/vault/
     */
    public function setCardVaulting(string $store_in_vault = 'ON_SUCCESS'): PayPal
    {
        return $this->mergeCardDetails('attributes', array_merge(
            $this->getCardAttributes(),
            ['vault' => ['store_in_vault' => $store_in_vault]]
        ));
    }

    /**
     * Set 3DS / SCA verification method for an ACDC order.
     *
     * Common values: SCA_ALWAYS (enforce 3DS always), SCA_WHEN_REQUIRED
     * (only when mandated by issuer/regulation), AUTHENTICATE_IF_REQUIRED.
     *
     * Can be combined with setCardVaulting() — both merge into
     * payment_source.card.attributes without overwriting each other.
     *
     * @see https://developer.paypal.com/docs/checkout/advanced/customize/3d-secure/
     */
    public function setCardVerification(string $method = 'SCA_ALWAYS'): PayPal
    {
        return $this->mergeCardDetails('attributes', array_merge(
            $this->getCardAttributes(),
            ['verification' => ['method' => $method]]
        ));
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
     * Merge a key into the existing payment_source.card object.
     *
     * @param array<string, mixed> $value
     */
    private function mergeCardDetails(string $key, array $value): PayPal
    {
        $current = $this->payment_source['card'] ?? null;
        $card = is_array($current) ? $current : [];
        $card[$key] = $value;

        return $this->setPaymentSourceDetails('card', $card);
    }

    /**
     * Return the current payment_source.card.attributes array, or [] if not set.
     *
     * @return array<string, mixed>
     */
    private function getCardAttributes(): array
    {
        $card = $this->payment_source['card'] ?? null;
        if (! is_array($card)) {
            return [];
        }
        $attributes = $card['attributes'] ?? null;

        return is_array($attributes) ? $attributes : [];
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
