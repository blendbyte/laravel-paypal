<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait WebHooksVerification
{
    /**
     * Verify a web hook from PayPal.
     *
     *
     *
     * @return array|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#verify-webhook-signature_post
     */
    public function verifyWebHook(array $data)
    {
        $this->apiEndPoint = 'v1/notifications/verify-webhook-signature';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
