<?php

namespace Blendbyte\PayPal\Traits;

trait PayPalExperienceContext
{
    /**
     * @var array
     */
    protected $experience_context = [];

    /**
     * Set Brand Name when setting experience context for payment.
     *
     * @param string $brand
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function setBrandName(string $brand): \Blendbyte\PayPal\Services\PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'brand_name' => $brand,
        ]);

        return $this;
    }

    /**
     * Set return & cancel urls.
     *
     * @param string $return_url
     * @param string $cancel_url
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function setReturnAndCancelUrl(string $return_url, string $cancel_url): \Blendbyte\PayPal\Services\PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
        ]);

        return $this;
    }

    /**
     * Set stored payment source.
     *
     * @param string      $initiator
     * @param string      $type
     * @param string      $usage
     * @param bool        $previous_reference
     * @param string|null $previous_transaction_id
     * @param string|null $previous_transaction_date
     * @param string|null $previous_transaction_reference_number
     * @param string|null $previous_transaction_network
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function setStoredPaymentSource(string $initiator, string $type, string $usage, bool $previous_reference = false, ?string $previous_transaction_id = null, ?string $previous_transaction_date = null, ?string $previous_transaction_reference_number = null, ?string $previous_transaction_network = null): \Blendbyte\PayPal\Services\PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'stored_payment_source' => [
                'payment_initiator' => $initiator,
                'payment_type'      => $type,
                'usage'             => $usage,
            ],
        ]);

        if ($previous_reference === true) {
            $this->experience_context['stored_payment_source']['previous_network_transaction_reference'] = [
                'id'                        => $previous_transaction_id,
                'date'                      => $previous_transaction_date,
                'acquirer_reference_number' => $previous_transaction_reference_number,
                'network'                   => $previous_transaction_network,
            ];
        }

        return $this;
    }
}
