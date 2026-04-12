<?php

namespace Srmklive\PayPal\Traits;

use Srmklive\PayPal\Services\PayPal;

trait PayPalExperienceContext
{
    /**
     * @var array<string, mixed>
     */
    protected $experience_context = [];

    /**
     * Set Brand Name when setting experience context for payment.
     */
    public function setBrandName(string $brand): PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'brand_name' => $brand,
        ]);

        return $this;
    }

    /**
     * Set return & cancel urls.
     */
    public function setReturnAndCancelUrl(string $return_url, string $cancel_url): PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
        ]);

        return $this;
    }

    /**
     * Set the server-side shipping address change callback URL.
     *
     * When the buyer changes their shipping address during checkout, PayPal
     * calls this URL so the merchant can recalculate shipping options/costs
     * before the order is confirmed. Requires Orders v2 (Feb 2025+).
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#definition-experience_context_base
     */
    public function setShippingAddressChangeCallback(string $url): PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'shipping_address_change_callback_url' => $url,
        ]);

        return $this;
    }

    /**
     * Set stored payment source.
     *
     * @param  string  $initiator  Payment initiator: CUSTOMER or MERCHANT
     * @param  string  $type       Payment type: ONE_TIME, RECURRING, or UNSCHEDULED
     * @param  string  $usage_pattern  Usage pattern (Feb 2025+): IMMEDIATE, DEFERRED, RESUBMISSION,
     *                                REAUTHORIZATION, NO_SHOW, DELAYED_CHARGE, or INCREMENTAL_AUTH
     */
    public function setStoredPaymentSource(string $initiator, string $type, string $usage_pattern, bool $previous_reference = false, ?string $previous_transaction_id = null, ?string $previous_transaction_date = null, ?string $previous_transaction_reference_number = null, ?string $previous_transaction_network = null): PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'stored_payment_source' => [
                'payment_initiator' => $initiator,
                'payment_type' => $type,
                'usage_pattern' => $usage_pattern,
            ],
        ]);

        if ($previous_reference === true) {
            $this->experience_context['stored_payment_source']['previous_network_transaction_reference'] = array_filter([
                'id' => $previous_transaction_id,
                'date' => $previous_transaction_date,
                'acquirer_reference_number' => $previous_transaction_reference_number,
                'network' => $previous_transaction_network,
            ], fn ($v) => $v !== null);
        }

        return $this;
    }
}
