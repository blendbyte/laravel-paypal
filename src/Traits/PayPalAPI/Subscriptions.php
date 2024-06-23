<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Carbon\Carbon;
use DateTimeInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

trait Subscriptions
{
    use Subscriptions\Helpers;

    /**
     * Create a new subscription.
     *
     * @param array $data
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_create
     */
    public function createSubscription(array $data): StreamInterface|array|string
    {
        $this->apiEndPoint = 'v1/billing/subscriptions';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Update an existing billing plan.
     *
     * @param string $subscription_id
     * @param array  $data
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_patch
     */
    public function updateSubscription(string $subscription_id, array $data): StreamInterface|array|string
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}";

        $this->options['json'] = $data;

        $this->verb = 'patch';

        return $this->doPayPalRequest(false);
    }

    /**
     * Show details for an existing subscription.
     *
     * @param string $subscription_id
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_get
     */
    public function showSubscriptionDetails(string $subscription_id): StreamInterface|array|string
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Activate an existing subscription.
     *
     * @param string $subscription_id
     * @param string $reason
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_activate
     */
    public function activateSubscription(string $subscription_id, string $reason): StreamInterface|array|string
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/activate";

        $this->options['json'] = ['reason' => $reason];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Cancel an existing subscription.
     *
     * @param string $subscription_id
     * @param string $reason
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_cancel
     */
    public function cancelSubscription(string $subscription_id, string $reason): StreamInterface|array|string
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/cancel";

        $this->options['json'] = ['reason' => $reason];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Suspend an existing subscription.
     *
     * @param string $subscription_id
     * @param string $reason
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_suspend
     */
    public function suspendSubscription(string $subscription_id, string $reason): StreamInterface|array|string
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/suspend";

        $this->options['json'] = ['reason' => $reason];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Capture payment for an existing subscription.
     *
     * @param string $subscription_id
     * @param string $note
     * @param float  $amount
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_capture
     */
    public function captureSubscriptionPayment(string $subscription_id, string $note, float $amount): StreamInterface|array|string
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/capture";

        $this->options['json'] = [
            'note'          => $note,
            'capture_type'  => 'OUTSTANDING_BALANCE',
            'amount'        => [
                'currency_code'     => $this->currency,
                'value'             => "{$amount}",
            ],
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Revise quantity, product or service for an existing subscription.
     *
     * @param string $subscription_id
     * @param array  $items
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_revise
     */
    public function reviseSubscription(string $subscription_id, array $items): StreamInterface|array|string
    {
        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/revise";

        $this->options['json'] = $items;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * List transactions for an existing subscription.
     *
     * @param string                   $subscription_id
     * @param DateTimeInterface|string $start_date
     * @param DateTimeInterface|string $end_date
     *
     * @throws Throwable
     *
     * @return array|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_transactions
     */
    public function listSubscriptionTransactions(string $subscription_id, DateTimeInterface|string $start_date = '', DateTimeInterface|string $end_date = ''): StreamInterface|array|string
    {
        if (($start_date instanceof DateTimeInterface) === false) {
            $start_date = Carbon::parse($start_date);
        }

        if (($end_date instanceof DateTimeInterface) === false) {
            $end_date = Carbon::parse($end_date);
        }

        $start_date = $start_date->toIso8601ZuluString();
        $end_date = $end_date->toIso8601ZuluString();

        $this->apiEndPoint = "v1/billing/subscriptions/{$subscription_id}/transactions?start_time={$start_date}&end_time={$end_date}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
