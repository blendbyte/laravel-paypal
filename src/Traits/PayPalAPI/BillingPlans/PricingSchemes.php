<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI\BillingPlans;

use Blendbyte\PayPal\Services\PayPal;
use Psr\Http\Message\StreamInterface;
use Throwable;

trait PricingSchemes
{
    protected $pricing_schemes = [];

    /**
     * Add pricing scheme for the billing plan.
     *
     *
     * @throws Throwable
     */
    public function addPricingScheme(string $interval_unit, int $interval_count, float $price, bool $trial = false): PayPal
    {
        $this->pricing_schemes[] = $this->addPlanBillingCycle($interval_unit, $interval_count, $price, 0, $trial);

        return $this;
    }

    /**
     * Process pricing updates for an existing billing plan.
     *
     *
     * @return array|StreamInterface|string
     *
     * @throws Throwable
     */
    public function processBillingPlanPricingUpdates()
    {
        return $this->updatePlanPricing($this->billing_plan['id'], $this->pricing_schemes);
    }
}
