<?php

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Srmklive\PayPal\Services\RetryPolicy;

describe('RetryPolicy::decider', function () {
    it('retries on 429 Too Many Requests', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(429);

        expect($decider(0, null, $response, null))->toBeTrue();
    });

    it('retries on 500 Internal Server Error', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(500);

        expect($decider(0, null, $response, null))->toBeTrue();
    });

    it('retries on 503 Service Unavailable', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(503);

        expect($decider(0, null, $response, null))->toBeTrue();
    });

    it('retries on ConnectException', function () {
        $decider = RetryPolicy::decider(3);
        $exception = new ConnectException('Connection refused', new Request('GET', '/'));

        expect($decider(0, null, null, $exception))->toBeTrue();
    });

    it('does not retry on 200 OK', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(200);

        expect($decider(0, null, $response, null))->toBeFalse();
    });

    it('does not retry on 400 Bad Request', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(400);

        expect($decider(0, null, $response, null))->toBeFalse();
    });

    it('stops retrying when maxRetries is reached', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(429);

        expect($decider(3, null, $response, null))->toBeFalse();
    });

    it('stops retrying when maxRetries is reached for 500', function () {
        $decider = RetryPolicy::decider(2);
        $response = new Response(500);

        expect($decider(2, null, $response, null))->toBeFalse();
    });

    it('returns false when no response and no exception', function () {
        $decider = RetryPolicy::decider(3);

        expect($decider(0, null, null, null))->toBeFalse();
    });
});

describe('RetryPolicy::delay', function () {
    it('reads Retry-After header on 429 and returns milliseconds', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(429, ['Retry-After' => '5']);

        expect($delay(1, $response))->toBe(5000);
    });

    it('returns 0ms for Retry-After: 0', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(429, ['Retry-After' => '0']);

        expect($delay(1, $response))->toBe(0);
    });

    it('returns 0ms minimum when Retry-After is negative', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(429, ['Retry-After' => '-10']);

        expect($delay(1, $response))->toBe(0);
    });

    it('falls back to exponential backoff when response is not 429', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(500);

        // retries=1 → 500 * 2^0 = 500ms
        expect($delay(1, $response))->toBe(500);
    });

    it('falls back to exponential backoff when response has no Retry-After', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(429);

        // retries=1 → 500ms (no Retry-After header)
        expect($delay(1, $response))->toBe(500);
    });

    it('falls back to exponential backoff when response is null', function () {
        $delay = RetryPolicy::delay();

        // retries=2 → 500 * 2^1 = 1000ms
        expect($delay(2, null))->toBe(1000);
    });

    it('exponential backoff caps at 8000ms', function () {
        $delay = RetryPolicy::delay();

        // retries=5 → 500 * 2^4 = 8000ms
        expect($delay(5, null))->toBe(8000);
    });
});
