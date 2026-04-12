<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait Trackers
{
    /**
     * Adds tracking information, with or without tracking numbers, for multiple PayPal transactions.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/tracking/v1/#trackers-batch_get
     */
    public function listTrackingDetails(string $transaction_id, ?string $tracking_number = null)
    {
        $params = ['transaction_id' => $transaction_id];

        if ($tracking_number !== null) {
            $params['tracking_number'] = $tracking_number;
        }

        $this->apiEndPoint = 'v1/shipping/trackers?'.http_build_query($params);

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Update tracking information.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
