<?php

namespace Srmklive\PayPal\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use RuntimeException;
use Srmklive\PayPal\Services\PayTheFly;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to verify PayTheFly webhook signatures.
 *
 * This middleware:
 * 1. Validates the webhook HMAC-SHA256 signature
 * 2. Checks timestamp tolerance (replay attack prevention)
 * 3. Rejects requests with invalid or expired signatures
 */
class VerifyPayTheFlyWebhook
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
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $payload = $request->all();

        $data      = $payload['data'] ?? null;
        $sign      = $payload['sign'] ?? null;
        $timestamp = $payload['timestamp'] ?? null;

        if ($data === null || $sign === null || $timestamp === null) {
            abort(400, 'Missing webhook signature fields.');
        }

        try {
            if (!$this->payTheFly->verifyWebhookSignature($data, $sign, (int) $timestamp)) {
                abort(403, 'Invalid webhook signature.');
            }
        } catch (RuntimeException $e) {
            abort(500, $e->getMessage());
        }

        return $next($request);
    }
}
