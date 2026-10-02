<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait Payouts
{
    /**
     * Create a Batch Payout.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments.payouts-batch/v1/#payouts_post
     */
    public function createBatchPayout(array $data)
    {
        $this->apiEndPoint = 'v1/payments/payouts';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Show Batch Payout details by ID.
     *
     * @param int|null $page           Page of payout items to return (1-1000).
     * @param int|null $page_size      Payout items per page (1-1000).
     * @param bool     $total_required Include the total item count in the response.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments.payouts-batch/v1/#payouts_get
     */
    public function showBatchPayoutDetails(string $payout_id, ?int $page = null, ?int $page_size = null, bool $total_required = false)
    {
        $query = http_build_query(array_filter([
            'page' => $page,
            'page_size' => $page_size,
            'total_required' => $total_required ? 'true' : null,
        ], fn ($value) => $value !== null));

        $this->apiEndPoint = "v1/payments/payouts/{$payout_id}".($query !== '' ? "?{$query}" : '');

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Show Payout Item details by ID.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments.payouts-batch/v1/#payouts-item_get
     */
    public function showPayoutItemDetails(string $payout_item_id)
    {
        $this->apiEndPoint = "v1/payments/payouts-item/{$payout_item_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Show Payout Item details by ID.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payments.payouts-batch/v1/#payouts-item_cancel
     */
    public function cancelUnclaimedPayoutItem(string $payout_item_id)
    {
        $this->apiEndPoint = "v1/payments/payouts-item/{$payout_item_id}/cancel";

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
