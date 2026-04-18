<?php

namespace Srmklive\PayPal\Traits;

use Srmklive\PayPal\Services\PayPal;
use RuntimeException;

trait PayPalRequest
{
    use PayPalAPI;
    use PayPalExperienceContext;
    use PayPalHttpClient;

    /**
     * PayPal API mode to be used.
     *
     * @var string
     */
    public $mode;

    /**
     * PayPal access token.
     *
     * @var string
     */
    protected $access_token;

    /**
     * PayPal API configuration.
     *
     * @var array<string, mixed>
     */
    private $config;

    /**
     * Default currency for PayPal.
     */
    protected string $currency = 'USD';

    /**
     * Additional options for PayPal API request.
     *
     * @var array<string, mixed>
     */
    protected $options;

    /**
     * Set limit to total records per API call.
     *
     * @var int
     */
    protected $page_size = 20;

    /**
     * Set the current page for list resources API calls.
     *
     * @var int
     */
    protected $current_page = 1;

    /**
     * Toggle whether totals for list resources are returned after every API call.
     */
    protected string $show_totals;

    /**
     * Set PayPal API Credentials.
     *
     *
     * @param array<string, mixed> $credentials
     *
     * @throws RuntimeException|\Exception
     */
    public function setApiCredentials(array $credentials): void
    {
        if (empty($credentials)) {
            $this->throwConfigurationException();
        }

        // Setting Default PayPal Mode If not set
        $this->setApiEnvironment($credentials);

        // Set API configuration for the PayPal provider
        $this->setApiProviderConfiguration($credentials);

        // Set default currency.
        $this->setCurrency($credentials['currency']);

        // Set Http Client configuration.
        $this->setHttpClientConfiguration();
    }

    /**
     * Function to set currency.
     *
     *
     * @throws RuntimeException
     */
    public function setCurrency(string $currency = 'USD'): PayPal
    {
        // Supported currencies per PayPal REST API docs:
        // https://developer.paypal.com/reference/currency-codes/
        // INR is retained for PayPal India (paypal.com/in) domestic accounts.
        // RUB was removed: PayPal suspended Russian services in March 2022.
        $allowedCurrencies = ['AUD', 'BRL', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'INR', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'SEK', 'SGD', 'THB', 'TWD', 'USD'];

        // Check if provided currency is valid.
        if (! in_array($currency, $allowedCurrencies, true)) {
            throw new RuntimeException("'{$currency}' is not a supported PayPal currency code. See https://developer.paypal.com/reference/currency-codes/ for the full list.");
        }

        $this->currency = $currency;

        return $this;
    }

    /**
     * Return the set currency.
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Set the PayPal-Partner-Attribution-Id (BN code) for all subsequent requests.
     *
     * PayPal uses this header to attribute transactions to a partner or platform.
     * Unlike the idempotency key, this header persists for the lifetime of the
     * provider instance — set it once after initialisation.
     *
     * @see https://developer.paypal.com/docs/api/reference/api-requests/#http-request-headers
     */
    public function setPartnerAttributionId(string $id): static
    {
        $this->setRequestHeader('PayPal-Partner-Attribution-Id', $id);

        return $this;
    }

    /**
     * Set a PayPal-Request-Id idempotency key for the next request.
     *
     * Sending this header allows safe retrying of failed requests without
     * risk of double-processing (e.g. duplicate charges). The key is
     * automatically cleared after the next API call.
     *
     * Pass null (default) to auto-generate a UUID v4.
     */
    public function withIdempotencyKey(?string $key = null): static
    {
        $this->setRequestHeader('PayPal-Request-Id', $key ?? $this->generateIdempotencyKey());

        return $this;
    }

    /**
     * Generate a random UUID v4 for use as an idempotency key.
     */
    private function generateIdempotencyKey(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant RFC 4122

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Function to add request header.
     */
    public function setRequestHeader(string $key, string $value): PayPal
    {
        $this->options['headers'][$key] = $value;

        return $this;
    }

    /**
     * Function to add multiple request headers.
     *
     * @param array<string, string> $headers
     */
    public function setRequestHeaders(array $headers): PayPal
    {
        foreach ($headers as $key => $value) {
            $this->setRequestHeader($key, $value);
        }

        return $this;
    }

    /**
     * Return request options header.
     *
     *
     * @throws RuntimeException
     */
    public function getRequestHeader(string $key): string
    {
        if (isset($this->options['headers'][$key])) {
            return $this->options['headers'][$key];
        }

        throw new RuntimeException('Options header is not set.');
    }

    /**
     * Function To Set PayPal API Configuration.
     *
     *
     * @param array<string, mixed> $config
     *
     * @throws \Exception
     */
    private function setConfig(array $config): void
    {
        $api_config = $config;
        if (empty($config) && function_exists('config')) {
            try {
                $fromLaravel = config('paypal');
                if (! empty($fromLaravel)) {
                    $api_config = $fromLaravel;
                }
            } catch (\Throwable) {
                // Not running in a full Laravel context
            }
        }

        // Set Api Credentials
        $this->setApiCredentials($api_config);
    }

    /**
     * Set API environment to be used by PayPal.
     *
     * @param array<string, mixed> $credentials
     */
    private function setApiEnvironment(array $credentials): void
    {
        $this->mode = 'live';

        if (! empty($credentials['mode'])) {
            $this->setValidApiEnvironment($credentials['mode']);
        } else {
            $this->throwConfigurationException();
        }
    }

    /**
     * Validate & set the environment to be used by PayPal.
     */
    private function setValidApiEnvironment(string $mode): void
    {
        $this->mode = ! in_array($mode, ['sandbox', 'live']) ? 'live' : $mode;
    }

    /**
     * Set configuration details for the provider.
     *
     *
     * @param array<string, mixed> $credentials
     *
     * @throws \Exception
     */
    private function setApiProviderConfiguration(array $credentials): void
    {
        // Setting PayPal API Credentials
        if (empty($credentials[$this->mode])) {
            $this->throwConfigurationException();
        }

        $config_params = ['client_id', 'client_secret'];

        foreach ($config_params as $item) {
            if (empty($credentials[$this->mode][$item])) {
                throw new RuntimeException("{$item} missing from the provided configuration. Please add your application {$item}.");
            }
        }

        foreach ($credentials[$this->mode] as $key => $value) {
            $this->config[$key] = $value;
        }

        $this->paymentAction = $credentials['payment_action'];

        $this->locale = $credentials['locale'];
        $this->setRequestHeader('Accept-Language', $this->locale);

        $this->validateSSL = $credentials['validate_ssl'];

        $this->setOptions($credentials);
    }

    /**
     * @throws RuntimeException
     */
    private function throwConfigurationException(): never
    {
        throw new RuntimeException('Invalid configuration provided. Please provide valid configuration for PayPal API. You can also refer to the documentation at https://blendbyte.github.io/laravel-paypal/docs.html to setup correct configuration.');
    }

    /**
     * @throws RuntimeException
     */
    private function throwInvalidEvidenceFileException(): never
    {
        throw new RuntimeException('Invalid evidence file type provided.
        1. The party can upload up to 50 MB of files per request.
        2. Individual files must be smaller than 10 MB.
        3. The supported file formats are JPG, JPEG, GIF, PNG, and PDF.
        ');
    }
}
