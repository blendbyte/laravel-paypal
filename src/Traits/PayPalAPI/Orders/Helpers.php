<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI\Orders;

use Throwable;

trait Helpers
{
    /**
     * Confirm payment for an order.
     *
     * @param string $order_id
     * @param string $processing_instruction
     *
     * @throws Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     */
    public function setupOrderConfirmation(string $order_id, string $processing_instruction = '')
    {
        $body = [
            'processing_instruction' => $processing_instruction,
            'application_context'    => $this->experience_context,
            'payment_source'         => $this->payment_source,
        ];

        return $this->confirmOrder($order_id, $body);
    }
}
