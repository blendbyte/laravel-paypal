<?php

use Srmklive\PayPal\Exceptions\PayPalApiException;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

uses(MockRequestPayloads::class);

/**
 * Build a Guzzle mock client that returns the given status code and body.
 */
function mockErrorClient(int $status, string $body): HttpClient
{
    $mock    = new MockHandler([new Response($status, [], $body)]);
    $handler = HandlerStack::create($mock);

    return new HttpClient(['handler' => $handler]);
}

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $this->client->getAccessToken();
});

// ── Default (silent) mode ─────────────────────────────────────────────────

it('returns error array by default on a failed request', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    $response = $this->client->showOrderDetails('bad-id');

    expect($response)->toHaveKey('error');
});

it('does not throw by default on a failed request', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    $exception = null;
    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        $exception = $e;
    }
    expect($exception)->toBeNull();
});

// ── Exception mode ────────────────────────────────────────────────────────

it('withExceptions() is fluent', function () {
    expect($this->client->withExceptions())->toBeInstanceOf(PayPalClient::class);
});

it('throws PayPalApiException on error when withExceptions() is enabled', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    expect(fn () => $this->client->showOrderDetails('bad-id'))->toThrow(PayPalApiException::class);
});

it('getPayPalError() returns the decoded error payload', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    $exception = null;
    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        $exception = $e;
    }
    expect($exception)->toBeInstanceOf(PayPalApiException::class);
    expect($exception->getPayPalError())->toBeArray();
    expect($exception->getPayPalError())->toHaveKey('name');
});

it('getMessage() contains the error information', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    $exception = null;
    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        $exception = $e;
    }
    expect($exception)->toBeInstanceOf(PayPalApiException::class);
    expect($exception->getMessage())->toContain('UNPROCESSABLE_ENTITY');
});

it('getPayPalError() returns a string for non-JSON error bodies', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(503, 'Service Unavailable'));

    $exception = null;
    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        $exception = $e;
    }
    expect($exception)->toBeInstanceOf(PayPalApiException::class);
    expect($exception->getPayPalError())->toBeString();
    expect($exception->getPayPalError())->toBe('Service Unavailable');
});

// ── HTTP status code ──────────────────────────────────────────────────────

it('getHttpStatus() returns the HTTP status code', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    $exception = null;
    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        $exception = $e;
    }
    expect($exception)->toBeInstanceOf(PayPalApiException::class);
    expect($exception->getHttpStatus())->toBe(422);
});

it('getHttpStatus() returns the correct code for different status codes', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions();
    $this->client->setClient(mockErrorClient(404, '{"name":"RESOURCE_NOT_FOUND","message":"Not found"}'));

    $exception = null;
    try {
        $this->client->showOrderDetails('bad-id');
    } catch (PayPalApiException $e) {
        $exception = $e;
    }
    expect($exception)->toBeInstanceOf(PayPalApiException::class);
    expect($exception->getHttpStatus())->toBe(404);
});

// ── decode=false error path ───────────────────────────────────────────────

it('returns error string for a decode=false endpoint on failure', function () {
    // updateBillingPlan() calls doPayPalRequest(false), so errors must still
    // surface as ['error' => <raw body string>] rather than being decoded.
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->setClient(mockErrorClient(400, 'Bad Request'));

    $response = $this->client->updatePlan('PLAN-123', []);

    expect($response)->toHaveKey('error');
    expect($response['error'])->toBe('Bad Request');
});

// ── JSON non-array/non-string primitive response ──────────────────────────

it('returns an empty array when the API response is the JSON null literal', function () {
    // Utils::jsonDecode('null', true) returns PHP null — neither array nor
    // string — so doPayPalRequest() must fall through to the [] branch.
    $mock    = new MockHandler([new Response(200, [], 'null')]);
    $handler = HandlerStack::create($mock);

    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->setClient(new HttpClient(['handler' => $handler]));

    $response = $this->client->showOrderDetails('some-id');

    expect($response)->toBe([]);
});

// ── PSR-18 transport exception ────────────────────────────────────────────

it('returns error array when the PSR-18 transport throws a ClientExceptionInterface', function () {
    // Inject a PSR-18 ClientInterface stub whose sendRequest() throws a
    // ClientExceptionInterface, exercising the catch block in makeHttpRequest()
    // that wraps it as a RuntimeException (line 306 of PayPalHttpClient).
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);

    $psr18Mock = new class implements \Psr\Http\Client\ClientInterface {
        public function sendRequest(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface
        {
            throw new class('Connection refused') extends \RuntimeException implements \Psr\Http\Client\ClientExceptionInterface {};
        }
    };
    $this->client->setClient($psr18Mock);

    $response = $this->client->showOrderDetails('some-id');

    expect($response)->toHaveKey('error');
    expect($response['error'])->toContain('Connection refused');
});

// ── Toggling back ─────────────────────────────────────────────────────────

it('withoutExceptions() reverts to silent error mode', function () {
    $this->client->setAccessToken(['access_token' => 'tok', 'token_type' => 'Bearer']);
    $this->client->withExceptions()->withoutExceptions();
    $this->client->setClient(mockErrorClient(422, '{"name":"UNPROCESSABLE_ENTITY","message":"Invalid request"}'));

    $response = $this->client->showOrderDetails('bad-id');

    expect($response)->toHaveKey('error');
});
