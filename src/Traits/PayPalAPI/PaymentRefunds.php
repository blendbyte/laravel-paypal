<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait PaymentRefunds
{
    /**
     * Show details for a refund.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#refunds_get
     */
    public function showRefundDetails(string $refund_id)
    {
        $this->apiEndPoint = "v2/payments/refunds/{$refund_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
