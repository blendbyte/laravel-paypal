<?php

namespace Srmklive\PayPal\Traits\PayPalAPI\Orders;

use Psr\Http\Message\StreamInterface;
use Throwable;

trait Helpers
{
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
     * If an experience_context has been set (via setReturnUrl(), setBrandName(),
     * etc.), it is nested inside the payment source method — matching the same
     * behaviour as setupOrderConfirmation().
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
     * Confirm payment for an order.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws Throwable
     */
    public function setupOrderConfirmation(string $order_id, string $processing_instruction = '')
    {
        $payment_source = $this->buildOrderPaymentSource();

        $body = [
            'processing_instruction' => $processing_instruction,
            'payment_source' => $payment_source,
        ];

        return $this->confirmOrder($order_id, $body);
    }

    /**
     * Build the order payment_source from the configured payment source,
     * experience context and stored credential.
     *
     * PayPal deprecated the top-level application_context in Orders v2:
     * experience_context and stored_credential are nested within the payment
     * source method. When no payment source is set, the paypal wallet is used.
     *
     * @return array<string, mixed>
     */
    private function buildOrderPaymentSource(): array
    {
        $payment_source = $this->payment_source;

        if (empty($this->experience_context) && empty($this->stored_credential)) {
            return $payment_source;
        }

        $method = empty($payment_source) ? 'paypal' : (string) array_key_first($payment_source);
        $details = $payment_source[$method] ?? [];

        if (! empty($this->experience_context)) {
            $details = array_merge($details, ['experience_context' => $this->experience_context]);
        }

        $stored_credential = $this->storedCredentialFor($method);

        if ($stored_credential !== null) {
            $details['stored_credential'] = $stored_credential;
        }

        $payment_source[$method] = $details;

        return $payment_source;
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
