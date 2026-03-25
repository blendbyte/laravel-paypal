<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI\Orders;

use Psr\Http\Message\StreamInterface;
use Throwable;

trait Helpers
{
    /**
     * Confirm payment for an order.
     *
     *
     *
     * @return array|StreamInterface|string
     *
     * @throws Throwable
     */
    public function setupOrderConfirmation(string $order_id, string $processing_instruction = '')
    {
        $body = [
            'processing_instruction' => $processing_instruction,
            'application_context' => $this->experience_context,
            'payment_source' => $this->payment_source,
        ];

        return $this->confirmOrder($order_id, $body);
    }
}
