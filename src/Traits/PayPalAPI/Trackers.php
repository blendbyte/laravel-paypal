<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI;

trait Trackers
{
    /**
     * Adds tracking information, with or without tracking numbers, for multiple PayPal transactions.
     *
     * @param array $data
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/tracking/v1/#trackers-batch_post
     */
    public function addBatchTracking(array $data)
    {
        $this->apiEndPoint = 'v1/shipping/trackers-batch';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Adds tracking information for a PayPal transaction.
     *
     * @param array $data
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/tracking/v1/#trackers_post
     */
    public function addTracking(array $data)
    {
        $this->apiEndPoint = 'v1/shipping/trackers';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * List tracking information based on Transaction ID or tracking number.
     *
     * @param string $transaction_id
     * @param string $tracking_number
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/tracking/v1/#trackers-batch_get
     */
    public function listTrackingDetails(string $transaction_id, ?string $tracking_number = null)
    {
        $this->apiEndPoint = "v1/shipping/trackers?transaction_id={$transaction_id}" . (!empty($tracking_number) ? "&tracking_number={$tracking_number}" : '');

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Update tracking information.
     *
     * @param string $tracking_id
     * @param array  $data
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/tracking/v1/#trackers_put
     */
    public function updateTrackingDetails(string $tracking_id, array $data)
    {
        $this->apiEndPoint = "v1/shipping/trackers/{$tracking_id}";

        $this->options['json'] = $data;

        $this->verb = 'put';

        return $this->doPayPalRequest(false);
    }

    /**
     * Show tracking information.
     *
     * @param string $tracking_id
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/tracking/v1/#trackers_get
     */
    public function showTrackingDetails(string $tracking_id)
    {
        $this->apiEndPoint = "v1/shipping/trackers/{$tracking_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
