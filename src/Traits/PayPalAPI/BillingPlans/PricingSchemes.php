<?php

namespace Srmklive\PayPal\Traits\PayPalAPI\BillingPlans;

use Srmklive\PayPal\Services\Amount;
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
     * Number of trial pricing schemes added to the current batch.
     */
    private int $pricing_scheme_trials = 0;

    /**
     * Add a new price for a billing cycle of an existing plan.
     *
     * The billing cycle is identified by its sequence in the plan. When
     * $sequence is omitted it is derived from the order of the calls: trial
     * cycles first (1, 2), then the regular cycle (number of trials + 1).
     * Pass $sequence explicitly if the plan's cycles are ordered differently.
     *
     * @param string   $interval_unit  Ignored: the billing frequency cannot be changed by a pricing update.
     * @param int      $interval_count Ignored: the billing frequency cannot be changed by a pricing update.
     * @param int|null $sequence       Billing cycle sequence (1-99) the price applies to.
     *
     * @throws \InvalidArgumentException When $sequence is outside 1-99.
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_update-pricing-schemes
     */
    public function addPricingScheme(string $interval_unit, int $interval_count, float $price, bool $trial = false, ?int $sequence = null): PayPal
    {
        if ($sequence !== null && ($sequence < 1 || $sequence > 99)) {
            throw new \InvalidArgumentException("Billing cycle sequence must be between 1 and 99, {$sequence} given.");
        }

        if ($trial) {
            $this->pricing_scheme_trials++;
        }

        $this->pricing_schemes[] = [
            'billing_cycle_sequence' => $sequence ?? ($trial ? $this->pricing_scheme_trials : $this->pricing_scheme_trials + 1),
            'pricing_scheme' => [
                'fixed_price' => [
                    'value' => Amount::format($price, $this->getCurrency()),
                    'currency_code' => $this->getCurrency(),
                ],
            ],
        ];

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
        $this->pricing_scheme_trials = 0;

        return $response;
    }
}
