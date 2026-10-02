<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Srmklive\PayPal\Services\Amount;
use Psr\Http\Message\StreamInterface;

trait PaymentAuthorizations
{
    /**
     * Show details for authorized payment.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_get
     */
    public function showAuthorizedPaymentDetails(string $authorization_id)
    {
        $this->apiEndPoint = "v2/payments/authorizations/{$authorization_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Capture an authorized payment.
     *
     * Empty $invoice_id / $note values are omitted (PayPal rejects empty strings).
     *
     * @param bool $final_capture Close the authorization after this capture. Pass false
     *                            to capture further amounts against it later.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_capture
     */
    public function captureAuthorizedPayment(string $authorization_id, string $invoice_id, float $amount, string $note, bool $final_capture = true)
    {
        $this->apiEndPoint = "v2/payments/authorizations/{$authorization_id}/capture";

        $this->options['json'] = array_filter([
            'amount' => [
                'value' => Amount::format($amount, $this->currency),
                'currency_code' => $this->currency,
            ],
            'invoice_id' => $invoice_id,
            'note_to_payer' => $note,
        ], fn ($value) => $value !== '') + ['final_capture' => $final_capture];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Reauthorize an authorized payment.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_reauthorize
     */
    public function reAuthorizeAuthorizedPayment(string $authorization_id, float $amount)
    {
        $this->apiEndPoint = "v2/payments/authorizations/{$authorization_id}/reauthorize";

        $this->options['json'] = [
            'amount' => [
                'value' => Amount::format($amount, $this->currency),
                'currency_code' => $this->currency,
            ],
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Void an authorized payment.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_void
     */
    public function voidAuthorizedPayment(string $authorization_id)
    {
        $this->apiEndPoint = "v2/payments/authorizations/{$authorization_id}/void";

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }
}
