<?php

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Srmklive\PayPal\Services\RetryPolicy;

describe('RetryPolicy::decider', function () {
    beforeEach(function () {
        $this->get = new Request('GET', '/v2/checkout/orders/O-1');
    });

    it('retries on 429 Too Many Requests', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(429);

        expect($decider(0, $this->get, $response, null))->toBeTrue();
    });

    it('retries on 500 Internal Server Error', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(500);

        expect($decider(0, $this->get, $response, null))->toBeTrue();
    });

    it('retries on 503 Service Unavailable', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(503);

        expect($decider(0, $this->get, $response, null))->toBeTrue();
    });

    it('retries on ConnectException', function () {
        $decider = RetryPolicy::decider(3);
        $exception = new ConnectException('Connection refused', $this->get);

        expect($decider(0, $this->get, null, $exception))->toBeTrue();
    });

    it('does not retry on 200 OK', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(200);

        expect($decider(0, $this->get, $response, null))->toBeFalse();
    });

    it('does not retry on 400 Bad Request', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(400);

        expect($decider(0, $this->get, $response, null))->toBeFalse();
    });

    it('stops retrying when maxRetries is reached', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(429);

        expect($decider(3, $this->get, $response, null))->toBeFalse();
    });

    it('stops retrying when maxRetries is reached for 500', function () {
        $decider = RetryPolicy::decider(2);
        $response = new Response(500);

        expect($decider(2, $this->get, $response, null))->toBeFalse();
    });

    it('returns false when no response and no exception', function () {
        $decider = RetryPolicy::decider(3);

        expect($decider(0, $this->get, null, null))->toBeFalse();
    });

    it('does not retry when the request is unknown', function () {
        $decider = RetryPolicy::decider(3);

        expect($decider(0, null, new Response(500), null))->toBeFalse();
    });

    it('retries idempotent methods', function (string $method) {
        $decider = RetryPolicy::decider(3);

        expect($decider(0, new Request($method, '/'), new Response(500), null))->toBeTrue();
    })->with(['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS']);

    it('does not retry POST/PATCH without an idempotency key', function (string $method) {
        $decider = RetryPolicy::decider(3);
        $request = new Request($method, '/v2/checkout/orders/O-1/capture');

        expect($decider(0, $request, new Response(500), null))->toBeFalse()
            ->and($decider(0, $request, new Response(429), null))->toBeFalse()
            ->and($decider(0, $request, null, new ConnectException('Timeout', $request)))->toBeFalse();
    })->with(['POST', 'PATCH']);

    it('retries POST/PATCH that carry a PayPal-Request-Id', function (string $method) {
        $decider = RetryPolicy::decider(3);
        $request = new Request($method, '/v2/checkout/orders/O-1/capture', ['PayPal-Request-Id' => 'key-1']);

        expect($decider(0, $request, new Response(500), null))->toBeTrue()
            ->and($decider(0, $request, null, new ConnectException('Timeout', $request)))->toBeTrue();
    })->with(['POST', 'PATCH']);

    it('retries a 429 whose Retry-After is within the limit', function () {
        $decider = RetryPolicy::decider(3);
        $response = new Response(429, ['Retry-After' => (string) RetryPolicy::MAX_RETRY_AFTER_SECONDS]);

        expect($decider(0, $this->get, $response, null))->toBeTrue();
    });

    it('does not retry a 429 whose Retry-After exceeds the limit', function () {
        $decider = RetryPolicy::decider(3);

        $seconds = new Response(429, ['Retry-After' => '3600']);
        $date = new Response(429, ['Retry-After' => gmdate('D, d M Y H:i:s \G\M\T', time() + 3600)]);

        expect($decider(0, $this->get, $seconds, null))->toBeFalse()
            ->and($decider(0, $this->get, $date, null))->toBeFalse();
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

    it('caps Retry-After at the maximum', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(429, ['Retry-After' => '3600']);

        expect($delay(1, $response))->toBe(RetryPolicy::MAX_RETRY_AFTER_SECONDS * 1000);
    });

    it('reads Retry-After given as an HTTP-date', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(429, ['Retry-After' => gmdate('D, d M Y H:i:s \G\M\T', time() + 5)]);

        // Allow for the clock ticking over between building and parsing the date.
        expect($delay(1, $response))->toBeGreaterThanOrEqual(4000)->toBeLessThanOrEqual(5000);
    });

    it('returns 0ms for a Retry-After HTTP-date in the past', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(429, ['Retry-After' => 'Wed, 21 Oct 2015 07:28:00 GMT']);

        expect($delay(1, $response))->toBe(0);
    });

    it('falls back to exponential backoff for an unparseable Retry-After', function () {
        $delay = RetryPolicy::delay();
        $response = new Response(429, ['Retry-After' => 'soon']);

        expect($delay(1, $response))->toBe(500);
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
