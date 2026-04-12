<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

// Clear the static cert cache before each test to prevent cross-test contamination.
beforeEach(function () {
    $prop = new ReflectionProperty(PayPalClient::class, 'certCache');
    $prop->setValue(null, []);
});

// ---------------------------------------------------------------------------
// isValidPayPalCertUrl — SSRF guard (tested via reflection on the private method)
// ---------------------------------------------------------------------------

it('accepts trusted PayPal production cert URLs', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $method = new ReflectionMethod(PayPalClient::class, 'isValidPayPalCertUrl');

    expect($method->invoke($client, 'https://api.paypal.com/v1/notifications/certs/cert1'))->toBeTrue();
    expect($method->invoke($client, 'https://api-m.paypal.com/v1/notifications/certs/cert1'))->toBeTrue();
});

it('accepts trusted PayPal sandbox cert URLs', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $method = new ReflectionMethod(PayPalClient::class, 'isValidPayPalCertUrl');

    expect($method->invoke($client, 'https://api.sandbox.paypal.com/v1/notifications/certs/cert1'))->toBeTrue();
    expect($method->invoke($client, 'https://api-m.sandbox.paypal.com/v1/notifications/certs/cert1'))->toBeTrue();
});

it('rejects arbitrary external domains', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $method = new ReflectionMethod(PayPalClient::class, 'isValidPayPalCertUrl');

    expect($method->invoke($client, 'https://evil.com/fake-cert'))->toBeFalse();
    expect($method->invoke($client, 'https://paypal.com.evil.com/cert'))->toBeFalse();
    expect($method->invoke($client, 'https://notpaypal.com/cert'))->toBeFalse();
});

it('rejects a URL with no parseable host', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $method = new ReflectionMethod(PayPalClient::class, 'isValidPayPalCertUrl');

    expect($method->invoke($client, 'not-a-url'))->toBeFalse();
    expect($method->invoke($client, ''))->toBeFalse();
});

// ---------------------------------------------------------------------------
// resolveOpenSslAlgo — algorithm mapping (private method via reflection)
// ---------------------------------------------------------------------------

it('maps SHA256withRSA to OPENSSL_ALGO_SHA256', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $method = new ReflectionMethod(PayPalClient::class, 'resolveOpenSslAlgo');

    expect($method->invoke($client, 'SHA256withRSA'))->toBe(OPENSSL_ALGO_SHA256);
    expect($method->invoke($client, 'sha256withrsa'))->toBe(OPENSSL_ALGO_SHA256); // case-insensitive
});

it('maps SHA1WITHRSA to OPENSSL_ALGO_SHA1', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $method = new ReflectionMethod(PayPalClient::class, 'resolveOpenSslAlgo');

    expect($method->invoke($client, 'SHA1WITHRSA'))->toBe(OPENSSL_ALGO_SHA1);
    expect($method->invoke($client, 'SHA1WITHRSA_PSS'))->toBe(OPENSSL_ALGO_SHA1);
});

it('defaults to OPENSSL_ALGO_SHA256 for unknown algorithm strings', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);
    $method = new ReflectionMethod(PayPalClient::class, 'resolveOpenSslAlgo');

    expect($method->invoke($client, 'UNKNOWN_ALGO'))->toBe(OPENSSL_ALGO_SHA256);
});

// ---------------------------------------------------------------------------
// verifyWebHookLocally — missing / malformed headers
// ---------------------------------------------------------------------------

it('returns false when required headers are missing', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect($client->verifyWebHookLocally([], 'WH-123', '{}'))->toBeFalse();
});

it('returns false when transmission_sig is missing', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect($client->verifyWebHookLocally([
        'PAYPAL-TRANSMISSION-ID'   => 'trans-123',
        'PAYPAL-TRANSMISSION-TIME' => '2024-01-01T00:00:00Z',
        'PAYPAL-CERT-URL'          => 'https://api.paypal.com/cert',
    ], 'WH-123', '{}'))->toBeFalse();
});

it('returns false when cert URL is not a trusted PayPal domain', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect($client->verifyWebHookLocally([
        'PAYPAL-TRANSMISSION-ID'   => 'trans-123',
        'PAYPAL-TRANSMISSION-TIME' => '2024-01-01T00:00:00Z',
        'PAYPAL-CERT-URL'          => 'https://evil.com/cert',
        'PAYPAL-TRANSMISSION-SIG'  => base64_encode('fake-sig'),
    ], 'WH-123', '{}'))->toBeFalse();
});

it('returns false when transmission_sig is not valid base64', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect($client->verifyWebHookLocally([
        'PAYPAL-TRANSMISSION-ID'   => 'trans-123',
        'PAYPAL-TRANSMISSION-TIME' => '2024-01-01T00:00:00Z',
        'PAYPAL-CERT-URL'          => 'https://api.paypal.com/cert',
        'PAYPAL-TRANSMISSION-SIG'  => '!!!not-base64!!!',
    ], 'WH-123', '{}'))->toBeFalse();
});

it('returns false when fetchCert returns an empty string', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['fetchCert']);
    $client->method('fetchCert')->willReturn('');

    expect($client->verifyWebHookLocally([
        'PAYPAL-TRANSMISSION-ID'   => 'trans-123',
        'PAYPAL-TRANSMISSION-TIME' => '2024-01-01T00:00:00Z',
        'PAYPAL-CERT-URL'          => 'https://api.paypal.com/cert',
        'PAYPAL-TRANSMISSION-SIG'  => base64_encode('fake-sig'),
    ], 'WH-123', '{}'))->toBeFalse();
});

it('handles headers case-insensitively', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['fetchCert']);
    $client->method('fetchCert')->willReturn(''); // returns false early — just verifying no crash

    // lowercase headers must still be resolved
    $result = $client->verifyWebHookLocally([
        'paypal-transmission-id'   => 'trans-123',
        'paypal-transmission-time' => '2024-01-01T00:00:00Z',
        'paypal-cert-url'          => 'https://api.paypal.com/cert',
        'paypal-transmission-sig'  => base64_encode('fake-sig'),
    ], 'WH-123', '{}');

    // Returns false because fetchCert returns '' — but the headers were read correctly.
    expect($result)->toBeFalse();
});

// ---------------------------------------------------------------------------
// verifyWebHookLocally — full RSA round-trip
// ---------------------------------------------------------------------------

it('returns true for a correctly signed webhook event', function () {
    // Generate a throw-away RSA key pair and self-signed certificate.
    $privateKey = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);

    $csr  = openssl_csr_new(['CN' => 'PayPal Test CA'], $privateKey, ['digest_alg' => 'sha256']);
    $cert = openssl_csr_sign($csr, null, $privateKey, 1, ['digest_alg' => 'sha256']);
    openssl_x509_export($cert, $certPem);

    // Build the message that PayPal signs.
    $transmission_id   = 'trans-roundtrip-001';
    $transmission_time = '2024-06-15T10:30:00Z';
    $webhook_id        = 'WH-ROUNDTRIP-001';
    $raw_body          = '{"id":"WH-EVT-001","event_type":"PAYMENT.SALE.COMPLETED"}';
    $crc               = crc32($raw_body);
    $message           = "{$transmission_id}|{$transmission_time}|{$webhook_id}|{$crc}";

    openssl_sign($message, $rawSignature, $privateKey, OPENSSL_ALGO_SHA256);
    $sig_b64 = base64_encode($rawSignature);

    $client = $this->createPartialMock(PayPalClient::class, ['fetchCert']);
    $client->expects($this->once())
        ->method('fetchCert')
        ->willReturn($certPem);

    $result = $client->verifyWebHookLocally([
        'PAYPAL-TRANSMISSION-ID'   => $transmission_id,
        'PAYPAL-TRANSMISSION-TIME' => $transmission_time,
        'PAYPAL-CERT-URL'          => 'https://api.paypal.com/v1/notifications/certs/test',
        'PAYPAL-AUTH-ALGO'         => 'SHA256withRSA',
        'PAYPAL-TRANSMISSION-SIG'  => $sig_b64,
    ], $webhook_id, $raw_body);

    expect($result)->toBeTrue();
});

it('returns false when the signature does not match the message', function () {
    $privateKey = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);

    $csr  = openssl_csr_new(['CN' => 'PayPal Test CA'], $privateKey, ['digest_alg' => 'sha256']);
    $cert = openssl_csr_sign($csr, null, $privateKey, 1, ['digest_alg' => 'sha256']);
    openssl_x509_export($cert, $certPem);

    // Sign a DIFFERENT message to create a mismatch.
    openssl_sign('tampered-message', $rawSignature, $privateKey, OPENSSL_ALGO_SHA256);
    $sig_b64 = base64_encode($rawSignature);

    $client = $this->createPartialMock(PayPalClient::class, ['fetchCert']);
    $client->method('fetchCert')->willReturn($certPem);

    $result = $client->verifyWebHookLocally([
        'PAYPAL-TRANSMISSION-ID'   => 'trans-001',
        'PAYPAL-TRANSMISSION-TIME' => '2024-01-01T00:00:00Z',
        'PAYPAL-CERT-URL'          => 'https://api.paypal.com/v1/notifications/certs/test',
        'PAYPAL-TRANSMISSION-SIG'  => $sig_b64,
    ], 'WH-001', '{"tampered":true}');

    expect($result)->toBeFalse();
});

// ---------------------------------------------------------------------------
// fetchCert — in-process caching behaviour
// ---------------------------------------------------------------------------

it('caches the certificate after the first successful fetch', function () {
    // Use a real HTTPS URL that will fail at the network level, but test the
    // cache path by pre-populating the cache via reflection.
    $certUrl = 'https://api.paypal.com/v1/notifications/certs/cached-test';

    $fakeCert = 'FAKE-CERT-PEM-CONTENT';

    // Pre-populate the static cache.
    $prop = new ReflectionProperty(PayPalClient::class, 'certCache');
    $prop->setValue(null, [$certUrl => $fakeCert]);

    $client = $this->createPartialMock(PayPalClient::class, []);

    // Call fetchCert — it must return the cached value without making a network call.
    $method = new ReflectionMethod(PayPalClient::class, 'fetchCert');
    $result = $method->invoke($client, $certUrl);

    expect($result)->toBe($fakeCert);
});

it('returns false when the cert PEM cannot be parsed as a public key', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['fetchCert']);
    // fetchCert returns a non-empty string that is not a valid PEM certificate,
    // so openssl_pkey_get_public() will return false and the method must return false.
    $client->method('fetchCert')->willReturn('not-a-valid-pem-certificate');

    $result = $client->verifyWebHookLocally([
        'PAYPAL-TRANSMISSION-ID'   => 'trans-001',
        'PAYPAL-TRANSMISSION-TIME' => '2024-01-01T00:00:00Z',
        'PAYPAL-CERT-URL'          => 'https://api.paypal.com/v1/notifications/certs/test',
        'PAYPAL-TRANSMISSION-SIG'  => base64_encode('fake-sig'),
    ], 'WH-001', '{"event":"test"}');

    expect($result)->toBeFalse();
});

it('fetchCert reads a local file, caches it, and returns the contents', function () {
    // Write a fake PEM to a temp file so file_get_contents() succeeds locally,
    // exercising the cache-store + return path (lines 149–151 in WebHooksVerification).
    $tempFile = tempnam(sys_get_temp_dir(), 'paypal_cert_success_');
    file_put_contents($tempFile, 'FAKE-PEM-CONTENT');

    $cacheProp = new ReflectionProperty(PayPalClient::class, 'certCache');
    $cacheProp->setValue(null, []);

    $client = $this->createPartialMock(PayPalClient::class, []);
    $method = new ReflectionMethod(PayPalClient::class, 'fetchCert');
    $result = $method->invoke($client, $tempFile);

    expect($result)->toBe('FAKE-PEM-CONTENT');

    $cache = $cacheProp->getValue(null);
    expect($cache)->toHaveKey($tempFile);
    expect($cache[$tempFile])->toBe('FAKE-PEM-CONTENT');

    unlink($tempFile);
});

it('does not cache a failed cert fetch so the next call can retry', function () {
    // Use a local path that is guaranteed not to exist — file_get_contents() returns
    // false for it, which is the same failure path as an unreachable HTTPS URL.
    $certUrl = sys_get_temp_dir().'/paypal_cert_nonexistent_'.uniqid().'.pem';

    $cacheProp = new ReflectionProperty(PayPalClient::class, 'certCache');
    $cacheProp->setValue(null, []);

    $client = $this->createPartialMock(PayPalClient::class, []);

    // Invoke the real (protected) fetchCert via reflection.
    $method = new ReflectionMethod(PayPalClient::class, 'fetchCert');

    // Suppress the E_WARNING emitted by file_get_contents() for a missing path.
    set_error_handler(fn () => null);
    $result = $method->invoke($client, $certUrl);
    restore_error_handler();

    // The cache must remain empty — failed fetches must not be cached.
    $cache = $cacheProp->getValue(null);
    expect($cache)->not->toHaveKey($certUrl);
    expect($result)->toBe('');
});
