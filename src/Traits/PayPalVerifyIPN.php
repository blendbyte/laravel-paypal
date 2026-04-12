<?php

namespace Srmklive\PayPal\Traits;

use Srmklive\PayPal\Services\PayPal;
use Illuminate\Http\Request;

trait PayPalVerifyIPN
{
    protected ?string $webhook_id = null;

    public function setWebHookID(string $webhook_id): PayPal
    {
        $this->webhook_id = $webhook_id;

        return $this;
    }

    /**
     * Verify incoming IPN through a web hook id.
     *
     *
     * @return array<string, mixed>|string
     *
     * @throws \Throwable
     */
    public function verifyIPN(Request $request)
    {
        $headers = array_change_key_case($request->headers->all(), CASE_UPPER);

        if (! isset($headers['PAYPAL-AUTH-ALGO'][0]) ||
            ! isset($headers['PAYPAL-TRANSMISSION-ID'][0]) ||
            ! isset($headers['PAYPAL-CERT-URL'][0]) ||
            ! isset($headers['PAYPAL-TRANSMISSION-SIG'][0]) ||
            ! isset($headers['PAYPAL-TRANSMISSION-TIME'][0]) ||
            ! isset($this->webhook_id)
        ) {
            return ['error' => 'Invalid headers or webhook id provided'];
        }

        $params = json_decode($request->getContent());

        if ($params === null) {
            return ['error' => 'Invalid or empty request body'];
        }

        $payload = [
            'auth_algo' => $headers['PAYPAL-AUTH-ALGO'][0],
            'cert_url' => $headers['PAYPAL-CERT-URL'][0],
            'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'][0],
            'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'][0],
            'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'][0],
            'webhook_id' => $this->webhook_id,
            'webhook_event' => $params,
        ];

        return $this->verifyWebHook($payload);
    }
}
