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
        $payment_source = $this->payment_source;

        if (! empty($this->experience_context)) {
            $method = empty($payment_source) ? 'paypal' : (string) array_key_first($payment_source);
            $payment_source[$method] = array_merge(
                $payment_source[$method] ?? [],
                ['experience_context' => $this->experience_context]
            );
        }

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
        $payment_source = $this->payment_source;

        // PayPal deprecated top-level application_context in the Orders v2 API.
        // experience_context must now be nested within the relevant payment source method.
        // When no explicit payment source is set, default to the paypal wallet key.
        if (! empty($this->experience_context)) {
            $method = empty($payment_source) ? 'paypal' : (string) array_key_first($payment_source);
            $payment_source[$method] = array_merge(
                $payment_source[$method] ?? [],
                ['experience_context' => $this->experience_context]
            );
        }

        $body = [
            'processing_instruction' => $processing_instruction,
            'payment_source' => $payment_source,
        ];

        return $this->confirmOrder($order_id, $body);
    }
}
