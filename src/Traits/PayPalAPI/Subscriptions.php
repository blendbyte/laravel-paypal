<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Psr\Http\Message\StreamInterface;

trait Subscriptions
{
    use Subscriptions\Helpers;

    /**
     * Create a new subscription.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_create
     */
    public function createSubscription(array $data)
    {
        $this->apiEndPoint = 'v1/billing/subscriptions';

        $this->options['json'] = $data;

        $this->verb = 'post';

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
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_patch
     */
    public function updateSubscription(string $subscription_id, array $data)
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}";

        $this->options['json'] = $data;

        $this->verb = 'patch';

        return $this->doPayPalRequest(false);
    }

    /**
     * Show details for an existing subscription.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_get
     */
    public function showSubscriptionDetails(string $subscription_id)
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Activate an existing subscription.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_activate
     */
    public function activateSubscription(string $subscription_id, string $reason)
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/activate";

        $this->options['json'] = ['reason' => $reason];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Cancel an existing subscription.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_cancel
     */
    public function cancelSubscription(string $subscription_id, string $reason)
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/cancel";

        $this->options['json'] = ['reason' => $reason];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Reactivate a suspended subscription with an optional reason.
     *
     * Convenience wrapper around activateSubscription() for the common
     * "resume after suspend" case — the reason defaults to a sensible value
     * so callers do not need to supply one.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_activate
     */
    public function reactivateSubscription(string $subscription_id, string $reason = 'Reactivating subscription'): array|StreamInterface|string
    {
        return $this->activateSubscription($subscription_id, $reason);
    }

    /**
     * Return true if the subscription's current status is ACTIVE.
     *
     * @throws \Throwable
     */
    public function isSubscriptionActive(string $subscription_id): bool
    {
        $details = $this->showSubscriptionDetails($subscription_id);

        return is_array($details) && ($details['status'] ?? '') === 'ACTIVE';
    }

    /**
     * Suspend an existing subscription.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_suspend
     */
    public function suspendSubscription(string $subscription_id, string $reason)
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/suspend";

        $this->options['json'] = ['reason' => $reason];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Capture payment for an existing subscription.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_capture
     */
    public function captureSubscriptionPayment(string $subscription_id, string $note, float $amount)
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/capture";

        $this->options['json'] = [
            'note' => $note,
            'capture_type' => 'OUTSTANDING_BALANCE',
            'amount' => [
                'currency_code' => $this->currency,
                'value' => number_format($amount, 2, '.', ''),
            ],
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Revise quantity, product or service for an existing subscription.
     *
     *
     *
     * @param array<string, mixed> $items
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_revise
     */
    public function reviseSubscription(string $subscription_id, array $items)
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/revise";

        $this->options['json'] = $items;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * List transactions for an existing subscription.
     *
     * @param  \DateTimeInterface|string  $start_date
     * @param  \DateTimeInterface|string  $end_date
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_transactions
     */
    public function listSubscriptionTransactions(string $subscription_id, $start_date = '', $end_date = '')
    {
        if (! ($start_date instanceof CarbonInterface)) {
            $start_date = Carbon::parse($start_date);
        }

        if (! ($end_date instanceof CarbonInterface)) {
            $end_date = Carbon::parse($end_date);
        }

        $start_date = $start_date->toIso8601ZuluString();
        $end_date = $end_date->toIso8601ZuluString();

        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/transactions?start_time={$start_date}&end_time={$end_date}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
