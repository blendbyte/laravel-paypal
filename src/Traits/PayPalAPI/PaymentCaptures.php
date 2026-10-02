<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Srmklive\PayPal\Services\Amount;
use Psr\Http\Message\StreamInterface;

trait PaymentCaptures
{
    /**
     * Show details for a captured payment.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#captures_get
     */
    public function showCapturedPaymentDetails(string $capture_id)
    {
        $this->apiEndPoint = "v2/payments/captures/{$capture_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Refund a captured payment.
     *
     * Empty $invoice_id / $note values are omitted (PayPal rejects empty strings).
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#captures_refund
     */
    public function refundCapturedPayment(string $capture_id, string $invoice_id, float $amount, string $note)
    {
        $this->apiEndPoint = "v2/payments/captures/{$capture_id}/refund";

        $this->options['json'] = array_filter([
            'amount' => [
                'value' => Amount::format($amount, $this->currency),
                'currency_code' => $this->currency,
            ],
            'invoice_id' => $invoice_id,
            'note_to_payer' => $note,
        ], fn ($value) => $value !== '');

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Refund the remaining amount of a captured payment.
     *
     * Omits the amount, so PayPal refunds the captured amount minus any
     * previous refunds. Empty $invoice_id / $note values are omitted.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#captures_refund
     */
    public function refundCapturedPaymentInFull(string $capture_id, string $invoice_id = '', string $note = '')
    {
        $this->apiEndPoint = "v2/payments/captures/{$capture_id}/refund";

        // The request body must be a JSON object, also when empty.
        $this->options['json'] = (object) array_filter([
            'invoice_id' => $invoice_id,
            'note_to_payer' => $note,
        ], fn ($value) => $value !== '');

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
