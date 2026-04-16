<?php

namespace Srmklive\PayPal\Traits\PayPalAPI\BillingPlans;

use Srmklive\PayPal\Services\PayPal;
use Psr\Http\Message\StreamInterface;
use Throwable;

trait PricingSchemes
{
    /**
     * @var list<array<string, mixed>>
     */
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
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws Throwable
     */
    public function processBillingPlanPricingUpdates()
    {
        if ($this->billing_plan === null) {
            throw new \RuntimeException('No billing plan set. Call addBillingPlanById() first.');
        }

        $response = $this->updatePlanPricing($this->billing_plan['id'], $this->pricing_schemes);

        // Reset so accumulated schemes don't bleed into subsequent calls.
        $this->pricing_schemes = [];

        return $response;
    }
}
