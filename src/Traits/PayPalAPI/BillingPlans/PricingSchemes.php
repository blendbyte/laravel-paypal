<?php

namespace Srmklive\PayPal\Traits\PayPalAPI\BillingPlans;

use Srmklive\PayPal\Services\Amount;
use Srmklive\PayPal\Services\PayPal;
use Psr\Http\Message\StreamInterface;
use Throwable;

trait PricingSchemes
{
    /**
     * Pending pricing updates: the new pricing scheme, whether it targets a
     * trial cycle, and the explicit billing cycle sequence (if given).
     *
     * @var list<array<string, mixed>>
     */
    protected $pricing_schemes = [];

    /**
     * Add a new price for a billing cycle of an existing plan.
     *
     * Without $sequence, the billing cycle is looked up in the plan when
     * processBillingPlanPricingUpdates() runs: trial prices are applied to
     * the plan's trial cycles in order, a regular price to its regular cycle.
     * Pass $sequence to target a billing cycle explicitly.
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

        $this->pricing_schemes[] = [
            'trial' => $trial,
            'billing_cycle_sequence' => $sequence,
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
     * If any price was added without an explicit sequence, the plan is
     * fetched first to map trial/regular prices to the plan's billing cycles;
     * an error from that lookup is returned as-is.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \RuntimeException When no billing plan is set or a price cannot be mapped to a billing cycle.
     * @throws Throwable
     */
    public function processBillingPlanPricingUpdates()
    {
        if ($this->billing_plan === null) {
            throw new \RuntimeException('No billing plan set. Call addBillingPlanById() first.');
        }

        $schemes = $this->pricing_schemes;

        // Reset so accumulated schemes don't bleed into subsequent calls.
        $this->pricing_schemes = [];

        if (in_array(null, array_column($schemes, 'billing_cycle_sequence'), true)) {
            $plan = $this->showPlanDetails($this->billing_plan['id']);

            if (! is_array($plan) || isset($plan['error'])) {
                return $plan;
            }

            $schemes = $this->resolvePricingSchemeSequences($schemes, $plan);
        }

        $pricing = array_values(array_map(fn ($scheme) => [
            'billing_cycle_sequence' => $scheme['billing_cycle_sequence'],
            'pricing_scheme' => $scheme['pricing_scheme'],
        ], $schemes));

        return $this->updatePlanPricing($this->billing_plan['id'], $pricing);
    }

    /**
     * Fill in missing sequences from the plan's billing cycles: the n-th trial
     * price goes to the n-th trial cycle, regular prices to the regular cycle.
     *
     * @param array<int, array<string, mixed>> $schemes
     * @param array<string, mixed>             $plan
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws \RuntimeException
     */
    private function resolvePricingSchemeSequences(array $schemes, array $plan): array
    {
        $trial_sequences = [];
        $regular_sequence = null;

        foreach ((array) ($plan['billing_cycles'] ?? []) as $cycle) {
            if (! is_array($cycle) || ! isset($cycle['sequence'])) {
                continue;
            }

            if (($cycle['tenure_type'] ?? null) === 'TRIAL') {
                $trial_sequences[] = (int) $cycle['sequence'];
            } elseif (($cycle['tenure_type'] ?? null) === 'REGULAR') {
                $regular_sequence = (int) $cycle['sequence'];
            }
        }

        sort($trial_sequences);
        $trial_index = 0;

        foreach ($schemes as $i => $scheme) {
            if ($scheme['billing_cycle_sequence'] !== null) {
                continue;
            }

            $sequence = $scheme['trial'] ? ($trial_sequences[$trial_index++] ?? null) : $regular_sequence;

            if ($sequence === null) {
                throw new \RuntimeException(sprintf(
                    'The plan has no matching %s billing cycle for this price. Pass the billing cycle sequence to addPricingScheme().',
                    $scheme['trial'] ? 'trial' : 'regular'
                ));
            }

            $schemes[$i]['billing_cycle_sequence'] = $sequence;
        }

        return $schemes;
    }
}
