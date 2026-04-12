<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait WebHooksVerification
{
    /**
     * In-process certificate cache keyed by URL.
     *
     * @var array<string, string>
     */
    private static array $certCache = [];

    /**
     * Verify a web hook from PayPal.
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#verify-webhook-signature_post
     */
    public function verifyWebHook(array $data)
    {
        $this->apiEndPoint = 'v1/notifications/verify-webhook-signature';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Verify a PayPal webhook signature locally without a PayPal API call.
     *
     * On the first call, the signing certificate is fetched over HTTPS from
     * the PayPal-supplied cert URL and cached in memory for the lifetime of
     * the process. All subsequent calls within the same process perform
     * pure in-memory RSA-SHA256 verification — no network I/O at all.
     *
     * For short-lived processes (e.g. single-invocation serverless functions)
     * the cert fetch happens on every cold start. For long-running processes
     * (PHP-FPM workers, queue workers) the cert is fetched once then reused
     * across thousands of events.
     *
     * The cert URL is validated against PayPal's known API domains before
     * any request is made (SSRF guard). The raw request body must be passed
     * unmodified — re-serialising the JSON will produce a different CRC32
     * and cause verification to fail.
     *
     * @param array<string, string> $headers    Raw webhook request headers (case-insensitive).
     * @param string                $webhook_id Webhook ID from your PayPal app configuration.
     * @param string                $raw_body   Unmodified raw request body bytes.
     *
     * @see https://developer.paypal.com/docs/api/webhooks/v1/#webhooks-verify-signatures-locally
     */
    public function verifyWebHookLocally(array $headers, string $webhook_id, string $raw_body): bool
    {
        $headers = array_change_key_case($headers, CASE_UPPER);

        $transmission_id   = $headers['PAYPAL-TRANSMISSION-ID'] ?? '';
        $transmission_time = $headers['PAYPAL-TRANSMISSION-TIME'] ?? '';
        $cert_url          = $headers['PAYPAL-CERT-URL'] ?? '';
        $auth_algo         = $headers['PAYPAL-AUTH-ALGO'] ?? 'SHA256withRSA';
        $transmission_sig  = $headers['PAYPAL-TRANSMISSION-SIG'] ?? '';

        if ($transmission_id === '' || $transmission_time === '' || $cert_url === '' || $transmission_sig === '') {
            return false;
        }

        if (! $this->isValidPayPalCertUrl($cert_url)) {
            return false;
        }

        $signature = base64_decode($transmission_sig, true);

        if ($signature === false) {
            return false;
        }

        $crc     = crc32($raw_body);
        $message = "{$transmission_id}|{$transmission_time}|{$webhook_id}|{$crc}";

        $cert = $this->fetchCert($cert_url);

        if ($cert === '') {
            return false;
        }

        $public_key = openssl_pkey_get_public($cert);

        if ($public_key === false) {
            return false;
        }

        return openssl_verify($message, $signature, $public_key, $this->resolveOpenSslAlgo($auth_algo)) === 1;
    }

    /**
     * Return true only if $url's host is a trusted PayPal API domain.
     */
    private function isValidPayPalCertUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host)) {
            return false;
        }

        return in_array($host, [
            'api.paypal.com',
            'api-m.paypal.com',
            'api.sandbox.paypal.com',
            'api-m.sandbox.paypal.com',
        ], true);
    }

    /**
     * Fetch and in-process-cache the PEM certificate from the given URL.
     *
     * Protected to allow override in tests without making real HTTP requests.
     */
    protected function fetchCert(string $url): string
    {
        if (isset(self::$certCache[$url])) {
            return self::$certCache[$url];
        }

        $context = stream_context_create([
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        $pem = file_get_contents($url, false, $context);

        if ($pem === false) {
            // Do NOT cache failures — let the next request retry the fetch.
            return '';
        }

        self::$certCache[$url] = $pem;

        return $pem;
    }

    /**
     * Map PayPal's auth_algo string to the matching OpenSSL constant.
     */
    private function resolveOpenSslAlgo(string $auth_algo): int
    {
        return match (strtoupper($auth_algo)) {
            'SHA1WITHRSA', 'SHA1WITHRSA_PSS' => OPENSSL_ALGO_SHA1,
            default                           => OPENSSL_ALGO_SHA256,
        };
    }
}
