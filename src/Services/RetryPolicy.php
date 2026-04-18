<?php

namespace Srmklive\PayPal\Services;

use Closure;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;

final class RetryPolicy
{
    /** @return Closure(int, mixed, mixed, mixed): bool */
    public static function decider(int $maxRetries): Closure
    {
        return static function (int $retries, mixed $request, mixed $response, mixed $exception) use ($maxRetries): bool {
            if ($retries >= $maxRetries) {
                return false;
            }

            return $exception instanceof ConnectException
                || ($response instanceof ResponseInterface && (
                    $response->getStatusCode() >= 500
                    || $response->getStatusCode() === 429
                ));
        };
    }

    /** @return Closure(int, mixed): int */
    public static function delay(): Closure
    {
        return static function (int $retries, mixed $response): int {
            if (
                $response instanceof ResponseInterface
                && $response->getStatusCode() === 429
                && $response->hasHeader('Retry-After')
            ) {
                return max(0, (int) $response->getHeaderLine('Retry-After')) * 1000;
            }

            // Exponential backoff: 500ms, 1s, 2s, 4s — capped at 8s.
            return (int) min(500 * (2 ** ($retries - 1)), 8000);
        };
    }
}
