<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

/**
 * Generate an RSA key pair and self-signed PEM certificate for testing.
 *
 * @return array{key: \OpenSSLAsymmetricKey, cert: string}
 */
function makeTestKeyAndCert(): array
{
    $key  = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    $csr  = openssl_csr_new([], $key, ['digest_alg' => 'sha256']);
    $x509 = openssl_csr_sign($csr, null, $key, 1, ['digest_alg' => 'sha256']);
    openssl_x509_export($x509, $cert);

    return ['key' => $key, 'cert' => $cert];
}

/**
 * Pre-populate the PayPalClient static cert cache via Reflection so tests
 * never make real HTTP requests to fetch the signing certificate.
 */
function injectPayPalCert(PayPalClient $client, string $url, string $cert): void
{
    $prop = (new ReflectionClass($client))->getProperty('certCache');
    /** @var array<string, string> $current */
    $current       = $prop->getValue() ?? [];
    $current[$url] = $cert;
    $prop->setValue(null, $current);
}

/**
 * Flush the PayPalClient static cert cache between tests.
 */
function flushPayPalCertCache(PayPalClient $client): void
{
    $prop = (new ReflectionClass($client))->getProperty('certCache');
    $prop->setValue(null, []);
}

/**
 * Build a correctly signed set of PayPal webhook headers.
 *
 * @param \OpenSSLAsymmetricKey $key
 * @return array<string, string>
 */
function signedWebHookHeaders(
    $key,
    string $transmission_id,
    string $transmission_time,
    string $webhook_id,
    string $raw_body,
    string $cert_url,
): array {
    $crc = crc32($raw_body);
    openssl_sign("{$transmission_id}|{$transmission_time}|{$webhook_id}|{$crc}", $sig, $key, OPENSSL_ALGO_SHA256);

    return [
        'PAYPAL-TRANSMISSION-ID'   => $transmission_id,
        'PAYPAL-TRANSMISSION-TIME' => $transmission_time,
        'PAYPAL-CERT-URL'          => $cert_url,
        'PAYPAL-AUTH-ALGO'         => 'SHA256withRSA',
        'PAYPAL-TRANSMISSION-SIG'  => base64_encode($sig),
    ];
}

const TEST_CERT_URL  = 'https://api.paypal.com/v1/notifications/certs/CERT-360caa42-fca2-a8-a1f3-48e26a181429';
const TEST_WEBHOOK_ID = '1JE4291016473214C';
const TEST_RAW_BODY  = '{"id":"WH-test-001","event_type":"PAYMENT.AUTHORIZATION.CREATED"}';

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $this->client->getAccessToken();

    flushPayPalCertCache($this->client);

    ['key' => $this->test_key, 'cert' => $this->test_cert] = makeTestKeyAndCert();
});

afterEach(function () {
    flushPayPalCertCache($this->client);
});

it('verifies a valid webhook signature locally', function () {
    injectPayPalCert($this->client, TEST_CERT_URL, $this->test_cert);

    $headers = signedWebHookHeaders(
        $this->test_key,
        '69cd13f0-d67a-11e5-baa3-778b53f4ae55',
        '2016-02-18T20:01:35Z',
        TEST_WEBHOOK_ID,
        TEST_RAW_BODY,
        TEST_CERT_URL,
    );

    expect($this->client->verifyWebHookLocally($headers, TEST_WEBHOOK_ID, TEST_RAW_BODY))->toBeTrue();
});

it('rejects a tampered request body', function () {
    injectPayPalCert($this->client, TEST_CERT_URL, $this->test_cert);

    $headers = signedWebHookHeaders(
        $this->test_key,
        '69cd13f0-d67a-11e5-baa3-778b53f4ae55',
        '2016-02-18T20:01:35Z',
        TEST_WEBHOOK_ID,
        TEST_RAW_BODY,
        TEST_CERT_URL,
    );

    expect($this->client->verifyWebHookLocally($headers, TEST_WEBHOOK_ID, 'tampered body content'))->toBeFalse();
});

it('rejects a tampered signature header', function () {
    injectPayPalCert($this->client, TEST_CERT_URL, $this->test_cert);

    $headers = signedWebHookHeaders(
        $this->test_key,
        '69cd13f0-d67a-11e5-baa3-778b53f4ae55',
        '2016-02-18T20:01:35Z',
        TEST_WEBHOOK_ID,
        TEST_RAW_BODY,
        TEST_CERT_URL,
    );

    $headers['PAYPAL-TRANSMISSION-SIG'] = base64_encode('not-a-valid-rsa-signature');

    expect($this->client->verifyWebHookLocally($headers, TEST_WEBHOOK_ID, TEST_RAW_BODY))->toBeFalse();
});

it('rejects a non-paypal cert URL', function () {
    $headers = [
        'PAYPAL-TRANSMISSION-ID'   => '69cd13f0-d67a-11e5-baa3-778b53f4ae55',
        'PAYPAL-TRANSMISSION-TIME' => '2016-02-18T20:01:35Z',
        'PAYPAL-CERT-URL'          => 'https://evil.example.com/cert.pem',
        'PAYPAL-AUTH-ALGO'         => 'SHA256withRSA',
        'PAYPAL-TRANSMISSION-SIG'  => base64_encode('sig'),
    ];

    expect($this->client->verifyWebHookLocally($headers, TEST_WEBHOOK_ID, TEST_RAW_BODY))->toBeFalse();
});

it('returns false when required headers are missing', function () {
    expect($this->client->verifyWebHookLocally([], TEST_WEBHOOK_ID, TEST_RAW_BODY))->toBeFalse();
});

it('does not cache a failed certificate fetch, allowing retry on next request', function () {
    // Regression: the old code cached empty string on failure (self::$certCache[$url] = ''),
    // so a transient network error permanently disabled verification for that URL.

    // Subclass with a controllable fetchCert so no real HTTP request is made.
    $client = new class ($this->getApiCredentials()) extends \Srmklive\PayPal\Services\PayPal {
        public int $fetchCount = 0;

        public string $certToReturn = '';

        protected function fetchCert(string $url): string
        {
            $this->fetchCount++;

            return $this->certToReturn;
        }
    };
    $client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $client->getAccessToken();

    $headers = signedWebHookHeaders(
        $this->test_key,
        '69cd13f0-d67a-11e5-baa3-778b53f4ae55',
        '2016-02-18T20:01:35Z',
        TEST_WEBHOOK_ID,
        TEST_RAW_BODY,
        TEST_CERT_URL,
    );

    // First call: fetchCert returns '' (simulated network error).
    $client->certToReturn = '';
    expect($client->verifyWebHookLocally($headers, TEST_WEBHOOK_ID, TEST_RAW_BODY))->toBeFalse();
    expect($client->fetchCount)->toBe(1);

    // Second call with a valid cert — if the empty string had been cached,
    // fetchCert would not be called again (count stays 1) and verification would fail.
    $client->certToReturn = $this->test_cert;
    expect($client->verifyWebHookLocally($headers, TEST_WEBHOOK_ID, TEST_RAW_BODY))->toBeTrue();
    expect($client->fetchCount)->toBe(2);
});

it('handles case-insensitive header names', function () {
    injectPayPalCert($this->client, TEST_CERT_URL, $this->test_cert);

    $headers = signedWebHookHeaders(
        $this->test_key,
        '69cd13f0-d67a-11e5-baa3-778b53f4ae55',
        '2016-02-18T20:01:35Z',
        TEST_WEBHOOK_ID,
        TEST_RAW_BODY,
        TEST_CERT_URL,
    );

    /** @var array<string, string> $lowercase */
    $lowercase = array_combine(
        array_map('strtolower', array_keys($headers)),
        array_values($headers)
    );

    expect($this->client->verifyWebHookLocally($lowercase, TEST_WEBHOOK_ID, TEST_RAW_BODY))->toBeTrue();
});
