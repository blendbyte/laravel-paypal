<?php

namespace Srmklive\PayPal\Services;

use Closure;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RetryPolicy
{
    /**
     * Longest Retry-After (in seconds) worth blocking the PHP process for.
     * Larger values are not retried; the 429 is returned to the caller.
     */
    public const MAX_RETRY_AFTER_SECONDS = 10;

    private const IDEMPOTENT_METHODS = ['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS'];

    /** @return Closure(int, mixed, mixed, mixed): bool */
    public static function decider(int $maxRetries): Closure
    {
        return static function (int $retries, mixed $request, mixed $response, mixed $exception) use ($maxRetries): bool {
            if ($retries >= $maxRetries || ! self::isSafeToRetry($request)) {
                return false;
            }

            if ($exception instanceof ConnectException) {
                return true;
            }

            if (! $response instanceof ResponseInterface) {
                return false;
            }

            if ($response->getStatusCode() === 429) {
                $retryAfter = self::retryAfterSeconds($response);

                return $retryAfter === null || $retryAfter <= self::MAX_RETRY_AFTER_SECONDS;
            }

            return $response->getStatusCode() >= 500;
        };
    }

    /** @return Closure(int, mixed): int */
    public static function delay(): Closure
    {
        return static function (int $retries, mixed $response): int {
            if ($response instanceof ResponseInterface && $response->getStatusCode() === 429) {
                $retryAfter = self::retryAfterSeconds($response);

                if ($retryAfter !== null) {
                    return min($retryAfter, self::MAX_RETRY_AFTER_SECONDS) * 1000;
                }
            }

            // Exponential backoff: 500ms, 1s, 2s, 4s — capped at 8s.
            return (int) min(500 * (2 ** ($retries - 1)), 8000);
        };
    }

    /**
     * Idempotent methods are always safe to resend. POST/PATCH are only resent
     * when they carry a PayPal-Request-Id, so PayPal can deduplicate them —
     * otherwise a retry after a 5xx/timeout could double-capture or double-pay.
     */
    private static function isSafeToRetry(mixed $request): bool
    {
        if (! $request instanceof RequestInterface) {
            return false;
        }

        return in_array(strtoupper($request->getMethod()), self::IDEMPOTENT_METHODS, true)
            || $request->getHeaderLine('PayPal-Request-Id') !== '';
    }

    /**
     * Parse a Retry-After header given in seconds or as an HTTP-date.
     * Returns null when the header is missing or unparseable.
     */
    private static function retryAfterSeconds(ResponseInterface $response): ?int
    {
        $value = trim($response->getHeaderLine('Retry-After'));

        if ($value === '') {
            return null;
        }

        if (preg_match('/^-?\d+$/', $value) === 1) {
            return max(0, (int) $value);
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? null : max(0, $timestamp - time());
    }
}
