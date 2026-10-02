<?php

namespace Srmklive\PayPal\Services;

use Srmklive\PayPal\Traits\PayPalRequest as PayPalAPIRequest;
use Srmklive\PayPal\Traits\PayPalVerifyIPN;
use Exception;

class PayPal
{
    use PayPalAPIRequest;
    use PayPalVerifyIPN;

    /**
     * PayPal constructor.
     *
     *
     * @param array<string, mixed> $config
     *
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        // Setting PayPal API Credentials
        $this->setConfig($config);

        $this->setRequestHeader('Accept', 'application/json');
    }

    /**
     * Set ExpressCheckout API endpoints & options.
     *
     * @param array<string, mixed> $credentials
     */
    protected function setOptions(array $credentials): void
    {
        // Setting API Endpoints
        $this->config['api_url'] = $this->mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        // Adding params outside sandbox / live array
        $this->config['timeout'] = (float) ($credentials['timeout'] ?? 30);
        $this->config['connect_timeout'] = (float) ($credentials['connect_timeout'] ?? 10);
        $this->config['max_retries'] = (int) ($credentials['max_retries'] ?? 2);
    }
}
