<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait BillingPlans
{
    use BillingPlans\PricingSchemes;

    /**
     * Create a new billing plan.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_create
     */
    public function createPlan(array $data)
    {
        $this->apiEndPoint = 'v1/billing/plans';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * List all billing plans.
     *
     * The shared page size (setPageSize()) is capped at 20, the maximum for
     * this endpoint. To filter by product use listPlansForProduct().
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_list
     */
    public function listPlans()
    {
        return $this->requestPlanList('');
    }

    /**
     * List the billing plans of a product.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_list
     */
    public function listPlansForProduct(string $product_id)
    {
        return $this->requestPlanList('&product_id='.rawurlencode($product_id));
    }

    /**
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     */
    private function requestPlanList(string $filter)
    {
        $page_size = max(1, min($this->page_size, 20));

        $this->apiEndPoint = "v1/billing/plans?page={$this->current_page}&page_size={$page_size}&total_required={$this->show_totals}{$filter}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Update an existing billing plan.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_patch
     */
    public function updatePlan(string $plan_id, array $data)
    {
        $this->apiEndPoint = "v1/billing/plans/{$plan_id}";

        $this->options['json'] = $data;

        $this->verb = 'patch';

        return $this->doPayPalRequest(false);
    }

    /**
     * Show details for an existing billing plan.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_get
     */
    public function showPlanDetails(string $plan_id)
    {
        $this->apiEndPoint = "v1/billing/plans/{$plan_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Activate an existing billing plan.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_activate
     */
    public function activatePlan(string $plan_id)
    {
        $this->apiEndPoint = "v1/billing/plans/{$plan_id}/activate";

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Deactivate an existing billing plan.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_deactivate
     */
    public function deactivatePlan(string $plan_id)
    {
        $this->apiEndPoint = "v1/billing/plans/{$plan_id}/deactivate";

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Update pricing for an existing billing plan.
     *
     *
     *
     * @param list<array<string, mixed>> $pricing
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans_update-pricing-schemes
     */
    public function updatePlanPricing(string $plan_id, array $pricing)
    {
        $this->apiEndPoint = "v1/billing/plans/{$plan_id}/update-pricing-schemes";

        $this->options['json'] = [
            'pricing_schemes' => $pricing,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }
}
