<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait WebHooks
{
    /**
     * Create a new web hook.
     *
     *
     *
     * @param array<string, mixed> $events
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_post
     */
    public function createWebHook(string $url, array $events)
    {
        $this->apiEndPoint = 'v1/notifications/webhooks';

        $data = ['url' => $url];
        $data['event_types'] = array_map(fn ($item) => ['name' => $item], $events);

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * List all web hooks.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_list
     */
    public function listWebHooks()
    {
        $this->apiEndPoint = 'v1/notifications/webhooks';

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Delete a web hook.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_delete
     */
    public function deleteWebHook(string $web_hook_id)
    {
        $this->apiEndPoint = "v1/notifications/webhooks/{$web_hook_id}";

        $this->verb = 'delete';

        return $this->doPayPalRequest(false);
    }

    /**
     * Update an existing web hook.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_update
     */
    public function updateWebHook(string $web_hook_id, array $data)
    {
        $this->apiEndPoint = "v1/notifications/webhooks/{$web_hook_id}";

        $this->options['json'] = $data;

        $this->verb = 'patch';

        return $this->doPayPalRequest();
    }

    /**
     * Show details for an existing web hook.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_get
     */
    public function showWebHookDetails(string $web_hook_id)
    {
        $this->apiEndPoint = "v1/notifications/webhooks/{$web_hook_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * List events for an existing web hook.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_get
     */
    public function listWebHookEvents(string $web_hook_id)
    {
        $this->apiEndPoint = "v1/notifications/webhooks/{$web_hook_id}/event-types";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
