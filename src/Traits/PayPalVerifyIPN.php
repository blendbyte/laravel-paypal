<?php

namespace Blendbyte\PayPal\Traits;

trait PayPalVerifyIPN
{
    protected $webhook_id;

    public function setWebHookID(string $webhook_id): \Blendbyte\PayPal\Services\PayPal
    {
        $this->webhook_id = $webhook_id;

        return $this;
    }

    /**
     * Verify incoming IPN through a web hook id.
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     */
    public function verifyIPN(\Illuminate\Http\Request $request)
    {
        $headers = array_change_key_case($request->headers->all(), CASE_UPPER);

        if (!isset($headers['PAYPAL-AUTH-ALGO'][0]) ||
            !isset($headers['PAYPAL-TRANSMISSION-ID'][0]) ||
            !isset($headers['PAYPAL-CERT-URL'][0]) ||
            !isset($headers['PAYPAL-TRANSMISSION-SIG'][0]) ||
            !isset($headers['PAYPAL-TRANSMISSION-TIME'][0]) ||
            !isset($this->webhook_id)
        ) {
            \Log::error('Invalid headers or webhook id supplied for paypal webhook');

            return ['error' => 'Invalid headers or webhook id provided'];
        }

        $params = json_decode($request->getContent());

        $payload = [
            'auth_algo'         => $headers['PAYPAL-AUTH-ALGO'][0],
            'cert_url'          => $headers['PAYPAL-CERT-URL'][0],
            'transmission_id'   => $headers['PAYPAL-TRANSMISSION-ID'][0],
            'transmission_sig'  => $headers['PAYPAL-TRANSMISSION-SIG'][0],
            'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'][0],
            'webhook_id'        => $this->webhook_id,
            'webhook_event'     => $params,
        ];

        return $this->verifyWebHook($payload);
    }
}
