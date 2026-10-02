<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait WebHooksEvents
{
    /**
     * List all events types for web hooks.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks-event-types_list
     */
    public function listEventTypes()
    {
        $this->apiEndPoint = 'v1/notifications/webhooks-event-types';

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * List all events notifications for web hooks.
     *
     * @param array<string, string|int> $filters Optional filters: page_size, start_time, end_time
     *                                           (RFC 3339 date-times), transaction_id, event_type.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks-events_list
     */
    public function listEvents(array $filters = [])
    {
        $query = http_build_query($filters, '', '&', PHP_QUERY_RFC3986);

        $this->apiEndPoint = 'v1/notifications/webhooks-events'.($query !== '' ? "?{$query}" : '');

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * List all events notifications for web hooks.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks-events_get
     */
    public function showEventDetails(string $event_id)
    {
        $this->apiEndPoint = "v1/notifications/webhooks-events/{$event_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Resend notification for the event.
     *
     *
     *
     * @param array<string, mixed> $items
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks-events_resend
     */
    public function resendEventNotification(string $event_id, array $items)
    {
        $this->apiEndPoint = "v1/notifications/webhooks-events/{$event_id}/resend";

        $this->options['json'] = [
            'webhook_ids' => $items,
        ];
        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Simulate a webhook event: PayPal posts a mock event to the webhook.
     *
     * Either $webhook_id or $url is required. Mock events cannot be verified
     * via verifyWebHook()/verifyIPN() (postback verification is not supported
     * for them); verify them locally with verifyWebHookLocally() using the
     * webhook ID string 'WEBHOOK_ID'.
     *
     * @param string      $event_type       A single subscribed event, e.g. PAYMENT.CAPTURE.COMPLETED.
     * @param string|null $webhook_id       ID of the webhook to send the event to.
     * @param string|null $url              Listener URL to send the event to (instead of a webhook ID).
     * @param string|null $resource_version Event resource version, e.g. '2.0'.
     *
     * @return array<string, mixed>|StreamInterface|string The simulated event.
     *
     * @throws \InvalidArgumentException When neither $webhook_id nor $url is given.
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#simulate-event_post
     * @see https://developer.paypal.com/api/rest/webhooks/simulator/
     */
    public function simulateWebHookEvent(string $event_type, ?string $webhook_id = null, ?string $url = null, ?string $resource_version = null)
    {
        if (($webhook_id === null || $webhook_id === '') && ($url === null || $url === '')) {
            throw new \InvalidArgumentException('Either a webhook ID or a URL is required to simulate a webhook event.');
        }

        $this->apiEndPoint = 'v1/notifications/simulate-event';

        $this->options['json'] = array_filter([
            'webhook_id' => $webhook_id,
            'url' => $url,
            'event_type' => $event_type,
            'resource_version' => $resource_version,
        ], fn ($value) => $value !== null && $value !== '');

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
