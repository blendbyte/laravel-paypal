<?php

namespace Srmklive\PayPal\Traits\PayPalAPI\Orders;

use Psr\Http\Message\StreamInterface;
use Throwable;

trait Helpers
{
    private const EXPERIENCE_CONTEXT_BASE_FIELDS = ['brand_name', 'locale', 'shipping_preference', 'return_url', 'cancel_url'];

    /**
     * experience_context fields supported per payment source (Orders v2).
     * Sources not listed here receive the experience context unfiltered.
     */
    private const EXPERIENCE_CONTEXT_FIELDS = [
        'paypal' => [
            'brand_name', 'locale', 'shipping_preference', 'contact_preference', 'return_url', 'cancel_url',
            'app_switch_context', 'landing_page', 'user_action', 'payment_method_preference', 'order_update_callback_config',
        ],
        'venmo' => ['brand_name', 'shipping_preference', 'order_update_callback_config', 'user_action'],
        'card' => ['return_url', 'cancel_url'],
        'apple_pay' => ['return_url', 'cancel_url'],
        'google_pay' => ['return_url', 'cancel_url'],
        'token' => [],
        'crypto' => ['locale', 'return_url', 'cancel_url'],
        'blik' => [...self::EXPERIENCE_CONTEXT_BASE_FIELDS, 'consumer_ip', 'consumer_user_agent'],
        'bancontact' => self::EXPERIENCE_CONTEXT_BASE_FIELDS,
        'eps' => self::EXPERIENCE_CONTEXT_BASE_FIELDS,
        'giropay' => self::EXPERIENCE_CONTEXT_BASE_FIELDS,
        'ideal' => self::EXPERIENCE_CONTEXT_BASE_FIELDS,
        'mybank' => self::EXPERIENCE_CONTEXT_BASE_FIELDS,
        'p24' => self::EXPERIENCE_CONTEXT_BASE_FIELDS,
        'sofort' => self::EXPERIENCE_CONTEXT_BASE_FIELDS,
        'trustly' => self::EXPERIENCE_CONTEXT_BASE_FIELDS,
    ];

    /**
     * Extract the capture (transaction) ID from a captured order response.
     *
     * After calling capturePaymentOrder(), the capture ID is buried at
     * purchase_units[0].payments.captures[0].id. This helper surfaces it
     * directly so callers don't need to navigate the nested structure.
     *
     * Returns null if the order has not been captured or the path is absent.
     */
    /**
     * @param array<string, mixed> $order
     */
    public function getCaptureIdFromOrder(array $order): ?string
    {
        return $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
    }

    /**
     * Create an order with the stored payment source automatically injected.
     *
     * Use this alongside setPaymentSourceApplePay(), setPaymentSourceGooglePay(),
     * setPaymentSourceVenmo(), setPaymentSourceCard(), or setPaymentSourcePayPal()
     * to avoid manually constructing the payment_source key in the order body.
     *
     * If an experience_context has been set (via setReturnAndCancelUrl(),
     * setBrandName(), etc.), the fields supported by the payment source are
     * nested inside it — matching the behaviour of setupOrderConfirmation().
     *
     * @param array<string, mixed> $data Order body (intent, purchase_units, etc.)
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws Throwable
     */
    public function createOrderWithPaymentSource(array $data)
    {
        $payment_source = $this->buildOrderPaymentSource();

        if (! empty($payment_source)) {
            $data['payment_source'] = $payment_source;
        }

        return $this->createOrder($data);
    }

    /**
     * Confirm the payment source for an order.
     *
     * Sends the payment source set via setPaymentSource*(), together with the
     * experience context and stored credential (see createOrderWithPaymentSource()).
     *
     * @param string $processing_instruction Deprecated and ignored: the confirm-payment-source
     *                                       API has no processing_instruction field.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws Throwable
     */
    public function setupOrderConfirmation(string $order_id, string $processing_instruction = '')
    {
        $payment_source = $this->buildOrderPaymentSource();

        // payment_source is a required object; send {} rather than [] when
        // nothing is set so PayPal returns its regular validation error.
        return $this->confirmOrder($order_id, [
            'payment_source' => empty($payment_source) ? new \stdClass() : $payment_source,
        ]);
    }

    /**
     * Build the order payment_source from the configured payment source,
     * experience context and stored credential.
     *
     * PayPal deprecated the top-level application_context in Orders v2:
     * experience_context and stored_credential are nested within the payment
     * source method. When no payment source is set, the paypal wallet is used.
     * Only the experience_context fields the payment source supports are sent.
     *
     * @return array<string, mixed>
     */
    private function buildOrderPaymentSource(): array
    {
        $payment_source = $this->payment_source;

        if (! empty($this->experience_context) || ! empty($this->stored_credential)) {
            $method = empty($payment_source) ? 'paypal' : (string) array_key_first($payment_source);
            $details = $payment_source[$method] ?? [];

            $experience_context = $this->experienceContextFor($method);

            if (! empty($experience_context)) {
                $details = array_merge($details, ['experience_context' => $experience_context]);
            }

            $stored_credential = $this->storedCredentialFor($method);

            if ($stored_credential !== null) {
                $details['stored_credential'] = $stored_credential;
            }

            $payment_source[$method] = $details;
        }

        // An empty method (e.g. setPaymentSourcePayPal([])) must be sent as a
        // JSON object, not an array.
        return array_map(fn ($details) => $details === [] ? new \stdClass() : $details, $payment_source);
    }

    /**
     * Filter the experience context to the fields the payment source supports.
     *
     * @return array<string, mixed>
     */
    private function experienceContextFor(string $method): array
    {
        if (! isset(self::EXPERIENCE_CONTEXT_FIELDS[$method])) {
            return $this->experience_context;
        }

        return array_intersect_key($this->experience_context, array_flip(self::EXPERIENCE_CONTEXT_FIELDS[$method]));
    }

    /**
     * Shape the stored credential for a payment source, or null when the
     * source does not support stored credentials.
     *
     * @return array<string, mixed>|null
     */
    private function storedCredentialFor(string $method): ?array
    {
        if (empty($this->stored_credential)) {
            return null;
        }

        $fields = match ($method) {
            'card', 'apple_pay' => ['payment_initiator', 'payment_type', 'usage', 'previous_network_transaction_reference'],
            'paypal' => ['payment_initiator', 'usage_pattern', 'usage'],
            default => null,
        };

        return $fields === null ? null : array_intersect_key($this->stored_credential, array_flip($fields));
    }
}
