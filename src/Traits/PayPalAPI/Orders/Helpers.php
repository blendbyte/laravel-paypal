<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI\Orders;

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
