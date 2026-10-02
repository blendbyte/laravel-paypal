<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait Disputes
{
    /**
     * List disputes.
     *
     * The shared page size (setPageSize()) is capped at 50, the maximum for
     * this endpoint.
     *
     * @param array<string, string|list<string>> $filters Optional query filters: dispute_state
     *                                                    (string or list), disputed_transaction_id,
     *                                                    update_time_before, update_time_after,
     *                                                    next_page_token.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_list
     */
    public function listDisputes(array $filters = [])
    {
        $query = ['page_size' => max(1, min($this->page_size, 50))];

        foreach ($filters as $name => $value) {
            $query[$name] = is_array($value) ? implode(',', $value) : $value;
        }

        $this->apiEndPoint = 'v1/customer/disputes?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Update a dispute.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_patch
     */
    public function updateDispute(string $dispute_id, array $data)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}";

        $this->options['json'] = $data;

        $this->verb = 'patch';

        // 202 returns a subsequent_action link as JSON; 204 has no body ([]).
        return $this->doPayPalRequest();
    }

    /**
     * Get dispute details.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_get
     */
    public function showDisputeDetails(string $dispute_id)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
