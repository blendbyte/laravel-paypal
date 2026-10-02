<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait PaymentMethodsEligibility
{
    /**
     * Find the payment methods a buyer is eligible for.
     *
     * The optional request body supports `customer` (country_code, channel,
     * id, email, phone), `purchase_units` (amount, payee) and `preferences`
     * (payment_flow, include_account_details, include_vault_tokens,
     * payment_source_constraint).
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#find-eligible-methods_find
     */
    public function findEligiblePaymentMethods(array $data = [])
    {
        $this->apiEndPoint = 'v2/payments/find-eligible-methods';

        // The request body must be a JSON object, also when empty.
        $this->options['json'] = (object) $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
