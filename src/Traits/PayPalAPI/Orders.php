<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait Orders
{
    use Orders\Helpers;

    /**
     * Creates an order.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_create
     */
    public function createOrder(array $data)
    {
        $this->apiEndPoint = 'v2/checkout/orders';

        $this->options['json'] = (object) $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Shows details for an order.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_get
     */
    public function showOrderDetails(string $order_id)
    {
        $this->apiEndPoint = "v2/checkout/orders/{$order_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Update order details.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_patch
     */
    public function updateOrder(string $order_id, array $data)
    {
        $this->apiEndPoint = "v2/checkout/orders/{$order_id}";

        $this->options['json'] = $data;

        $this->verb = 'patch';

        return $this->doPayPalRequest(false);
    }

    /**
     * Confirm the order.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     */
    public function confirmOrder(string $order_id, array $data)
    {
        $this->apiEndPoint = "v2/checkout/orders/{$order_id}/confirm-payment-source";

        $this->options['json'] = (object) $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Authorizes payment for an order.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_authorize
     */
    public function authorizePaymentOrder(string $order_id, array $data = [])
    {
        $this->apiEndPoint = "v2/checkout/orders/{$order_id}/authorize";

        $this->options['json'] = (object) $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Captures payment for an order.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     */
    public function capturePaymentOrder(string $order_id, array $data = [])
    {
        $this->apiEndPoint = "v2/checkout/orders/{$order_id}/capture";

        $this->options['json'] = (object) $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Add tracking information for an Order.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_track_create
     */
    public function addTrackingForOrder(string $order_id, array $data)
    {
        $this->apiEndPoint = "v2/checkout/orders/{$order_id}/track";

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Update tracking information for an Order.
     *
     * Sends a JSON Patch array. PayPal supports replacing `items`, replacing or
     * adding `notify_payer`, and replacing `status` with CANCELLED (see
     * cancelTrackingForOrder()). The tracker ID is returned by
     * addTrackingForOrder() in purchase_units[].shipping.trackers[].id.
     *
     * @param list<array<string, mixed>> $patch JSON Patch operations.
     *
     * @return array<string, mixed>|StreamInterface|string Empty array on success (204).
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_trackers_patch
     */
    public function updateTrackingForOrder(string $order_id, string $tracker_id, array $patch)
    {
        $this->apiEndPoint = "v2/checkout/orders/{$order_id}/trackers/{$tracker_id}";

        $this->options['json'] = $patch;

        $this->verb = 'patch';

        return $this->doPayPalRequest();
    }

    /**
     * Cancel tracking information for an Order.
     *
     * @return array<string, mixed>|StreamInterface|string Empty array on success (204).
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_trackers_patch
     */
    public function cancelTrackingForOrder(string $order_id, string $tracker_id)
    {
        return $this->updateTrackingForOrder($order_id, $tracker_id, [
            ['op' => 'replace', 'path' => '/status', 'value' => 'CANCELLED'],
        ]);
    }
}
