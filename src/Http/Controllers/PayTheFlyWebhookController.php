<?php

namespace Srmklive\PayPal\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Srmklive\PayPal\Events\PayTheFlyWebhookReceived;
use Srmklive\PayPal\Events\PayTheFlyPaymentConfirmed;
use Srmklive\PayPal\Services\PayTheFly;

/**
 * Handles incoming PayTheFly webhook notifications.
 *
 * This controller:
 * 1. Parses and verifies the webhook payload (signature already verified by middleware)
 * 2. Dispatches Laravel events for downstream processing
 * 3. Returns "success" response as required by PayTheFly
 */
class PayTheFlyWebhookController extends Controller
{
    /**
     * @var PayTheFly
     */
    protected PayTheFly $payTheFly;

    public function __construct(PayTheFly $payTheFly)
    {
        $this->payTheFly = $payTheFly;
    }

    /**
     * Handle a PayTheFly webhook.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Parse the verified webhook data
        $data = $this->payTheFly->parseWebhook($payload);

        // Extract structured payment data
        $paymentData = $this->payTheFly->extractPaymentData($data);

        // Dispatch generic webhook event
        event(new PayTheFlyWebhookReceived($data, $paymentData));

        // Dispatch specific event for confirmed payments
        if ($paymentData['confirmed'] && $paymentData['tx_type'] === 'payment') {
            event(new PayTheFlyPaymentConfirmed($paymentData));
        }

        // PayTheFly requires the response body to contain "success"
        return response()->json($this->payTheFly->webhookSuccessResponse());
    }
}
