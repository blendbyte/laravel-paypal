<?php

namespace Srmklive\PayPal\Traits;

use Srmklive\PayPal\Exceptions\PayPalApiException;
use Srmklive\PayPal\Services\RetryPolicy;
use Srmklive\PayPal\Services\Str;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

trait PayPalHttpClient
{
    /**
     * Http Client class object.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * Http Client configuration.
     *
     * @var array<int, mixed>
     */
    private $httpClientConfig;

    /**
     * PayPal API Endpoint.
     *
     * @var string
     */
    private $apiUrl;

    /**
     * PayPal API Endpoint.
     *
     * @var string
     */
    private $apiEndPoint;

    /**
     * IPN notification url for PayPal.
     *
     * @var string
     */
    private $notifyUrl;

    /**
     * Http Client request body parameter name.
     *
     * @var string
     */
    private $httpBodyParam;

    /**
     * Default payment action for PayPal.
     *
     * @var string
     */
    private $paymentAction;

    /**
     * Default locale for PayPal.
     *
     * @var string
     */
    private $locale;

    /**
     * Validate SSL details when creating HTTP client.
     * Null means "not yet set by config"; setDefaultValues() will default it to true.
     */
    private ?bool $validateSSL = null;

    /**
     * Request type.
     *
     * @var string
     */
    protected $verb = 'post';

    /**
     * When true, doPayPalRequest() throws PayPalApiException on error
     * instead of returning ['error' => ...].
     *
     * @var bool
     */
    private bool $throwOnError = false;

    /**
     * Enable exception mode: API errors throw PayPalApiException instead
     * of returning ['error' => ...].
     *
     * This is opt-in and non-breaking — existing code that checks
     * $response['error'] continues to work until this is called.
     */
    public function withExceptions(): static
    {
        $this->throwOnError = true;

        return $this;
    }

    /**
     * Revert to the default silent-error mode: API errors return
     * ['error' => ...] rather than throwing.
     */
    public function withoutExceptions(): static
    {
        $this->throwOnError = false;

        return $this;
    }

    /**
     * Set curl constants if not defined.
     *
     * @return void
     */
    protected function setCurlConstants()
    {
        $constants = [
            'CURLOPT_SSLVERSION' => 32,
            'CURL_SSLVERSION_TLSv1_2' => 6,
            'CURLOPT_SSL_VERIFYPEER' => 64,
            'CURLOPT_SSLCERT' => 10025,
        ];

        foreach ($constants as $key => $item) {
            $this->defineCurlConstant($key, $item);
        }
    }

    /**
     * Declare a curl constant.
     *
     * @param  string  $value
     * @return bool
     */
    protected function defineCurlConstant(string $key, string|int $value)
    {
        return defined($key) ? true : define($key, $value);
    }

    /**
     * Initialise or replace the HTTP client.
     *
     * Pass any PSR-18 {@see ClientInterface} to use a custom HTTP client
     * (e.g. Symfony HttpClient, Buzz, etc.). Pass null (the default) to
     * build the bundled Guzzle client with the configured timeout and
     * optional exponential-backoff retry middleware.
     *
     * Note: the retry middleware is only active on the default Guzzle client.
     * When injecting a custom client, handle retries externally if needed.
     *
     * @return void
     */
    public function setClient(?ClientInterface $client = null)
    {
        if ($client !== null) {
            $this->client = $client;

            return;
        }

        $timeout = (float) ($this->config['timeout'] ?? 30);
        $connectTimeout = (float) ($this->config['connect_timeout'] ?? 10);
        $maxRetries = (int) ($this->config['max_retries'] ?? 2);

        $stack = HandlerStack::create();

        if ($maxRetries > 0) {
            $stack->push(Middleware::retry(
                RetryPolicy::decider($maxRetries),
                RetryPolicy::delay()
            ));
        }

        $this->client = new Client([
            'handler' => $stack,
            'curl' => $this->httpClientConfig,
            'timeout' => $timeout,
            'connect_timeout' => $connectTimeout,
        ]);
    }

    /**
     * Function to set Http Client configuration.
     *
     * @return void
     */
    protected function setHttpClientConfiguration()
    {
        $this->setCurlConstants();

        $this->httpClientConfig = [
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_SSL_VERIFYPEER => $this->validateSSL,
        ];

        // Initialize Http Client
        $this->setClient();

        // Set default values.
        $this->setDefaultValues();

        // Set PayPal IPN Notification URL
        $this->notifyUrl = $this->config['notify_url'];
    }

    /**
     * Set default values for configuration.
     *
     * @return void
     */
    private function setDefaultValues()
    {
        $paymentAction = empty($this->paymentAction) ? 'Sale' : $this->paymentAction;
        $this->paymentAction = $paymentAction;

        $locale = empty($this->locale) ? 'en_US' : $this->locale;
        $this->locale = $locale;

        // Use null-coalescing assignment so that an explicit false is preserved.
        // empty(false) === true, so the old ternary silently reset false to true,
        // making it impossible to disable SSL verification via config.
        $this->validateSSL ??= true;

        $this->showTotals(true);
    }

    /**
     * Perform PayPal API request & return response.
     *
     * Builds a PSR-7 request from the current verb, URL, and options, then
     * dispatches it via the injected PSR-18 client. A response with status
     * >= 400 is treated as an error and re-thrown as RuntimeException so
     * that doPayPalRequest() can normalise it into an ['error' => ...] array.
     *
     * @throws \Throwable
     */
    private function makeHttpRequest(): StreamInterface
    {
        $factory = new HttpFactory();
        $request = $factory->createRequest(strtoupper($this->verb), $this->apiUrl);

        // Apply headers. Skip a manually set Content-Type for multipart
        // requests — the correct value (including boundary) is set below.
        /** @var array<string, string> $headers */
        $headers = is_array($this->options['headers'] ?? null) ? $this->options['headers'] : [];
        foreach ($headers as $name => $value) {
            if (isset($this->options['multipart']) && strtolower($name) === 'content-type') {
                continue;
            }
            $request = $request->withHeader($name, $value);
        }

        // Apply HTTP Basic Auth when options['auth'] is set (used by getAccessToken()).
        if (isset($this->options['auth']) && is_array($this->options['auth'])) {
            [$user, $pass] = $this->options['auth'];
            $request = $request->withHeader('Authorization', 'Basic '.base64_encode("{$user}:{$pass}"));
        }

        if (isset($this->options['json'])) {
            $body = Utils::jsonEncode($this->options['json']);
            $request = $request
                ->withBody($factory->createStream($body))
                ->withHeader('Content-Type', 'application/json');
        } elseif (isset($this->options['multipart'])) {
            /** @var array<mixed> $parts */
            $parts = is_array($this->options['multipart']) ? $this->options['multipart'] : [];
            $multipart = new MultipartStream($parts);
            $request = $request
                ->withBody($multipart)
                ->withHeader('Content-Type', 'multipart/form-data; boundary='.$multipart->getBoundary());
        } elseif (isset($this->options['form_params']) && is_array($this->options['form_params'])) {
            // URL-encoded form body (used by getAccessToken()).
            $body = http_build_query($this->options['form_params'], '', '&', PHP_QUERY_RFC1738);
            $request = $request
                ->withBody($factory->createStream($body))
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        if ($response->getStatusCode() >= 400) {
            throw new RuntimeException((string) $response->getBody(), $response->getStatusCode());
        }

        return $response->getBody();
    }

    /**
     * Function To Perform PayPal API Request.
     *
     *
     *
     * @return array<string, mixed>|string
     *
     * @throws \Throwable
     */
    private function doPayPalRequest(bool $decode = true)
    {
        try {
            $this->apiUrl = implode('/', [$this->config['api_url'], $this->apiEndPoint]);

            // Perform PayPal HTTP API request.
            $response = $this->makeHttpRequest();

            // Idempotency key is single-use — clear it after the request.
            unset($this->options['headers']['PayPal-Request-Id']);

            if ($decode === false) {
                return $response->getContents();
            }

            $decoded = Utils::jsonDecode($response, true);

            return is_array($decoded) ? $decoded : (is_string($decoded) ? $decoded : []);
        } catch (RuntimeException $t) {
            unset($this->options['headers']['PayPal-Request-Id']);

            // Decode JSON error bodies; fall back to the raw message string for
            // non-JSON responses (network timeouts, plain-text errors, etc.).
            $decoded = ($decode === false) || (Str::isJson($t->getMessage()) === false)
                ? null
                : Utils::jsonDecode($t->getMessage(), true);

            $error = is_array($decoded) ? $decoded : $t->getMessage();

            if ($this->throwOnError) {
                throw new PayPalApiException($error, $t->getCode(), $t);
            }

            return ['error' => $error];
        }
    }
}
