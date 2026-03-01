<?php

namespace Srmklive\PayPal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when any PayTheFly webhook is received and verified.
 *
 * Listen for this event to handle all webhook types (payments, withdrawals).
 *
 * Usage in EventServiceProvider:
 *
 *     PayTheFlyWebhookReceived::class => [
 *         YourWebhookHandler::class,
 *     ],
 */
class PayTheFlyWebhookReceived
{
    use Dispatchable, SerializesModels;

    /**
     * Raw parsed webhook data.
     *
     * @var array
     */
    public array $rawData;

    /**
     * Structured payment data extracted from webhook.
     *
     * @var array
     */
    public array $paymentData;

    /**
     * Create a new event instance.
     *
     * @param array $rawData
     * @param array $paymentData
     */
    public function __construct(array $rawData, array $paymentData)
    {
        $this->rawData     = $rawData;
        $this->paymentData = $paymentData;
    }
}
