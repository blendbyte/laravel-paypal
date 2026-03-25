<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI;

trait PaymentAuthorizations
{
    /**
     * Show details for authorized payment.
     *
     * @param string $authorization_id
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
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
     * @param string $authorization_id
     * @param string $invoice_id
     * @param float  $amount
     * @param string $note
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_capture
     */
    public function captureAuthorizedPayment(string $authorization_id, string $invoice_id, float $amount, string $note)
    {
        $this->apiEndPoint = "v2/payments/authorizations/{$authorization_id}/capture";

        $this->options['json'] = [
            'amount' => [
                'value'         => $amount,
                'currency_code' => $this->currency,
            ],
            'invoice_id'    => $invoice_id,
            'note_to_payer' => $note,
            'final_capture' => true,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Reauthorize an authorized payment.
     *
     * @param string $authorization_id
     * @param float  $amount
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_reauthorize
     */
    public function reAuthorizeAuthorizedPayment(string $authorization_id, float $amount)
    {
        $this->apiEndPoint = "v2/payments/authorizations/{$authorization_id}/reauthorize";

        $this->options['json'] = [
            'amount' => [
                'value'         => $amount,
                'currency_code' => $this->currency,
            ],
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Void an authorized payment.
     *
     * @param string $authorization_id
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
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
