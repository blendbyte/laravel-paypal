<?php

namespace Srmklive\PayPal\Traits;

use GuzzleHttp\Client as HttpClient;

trait PayTheFlyRequest
{
    /**
     * Http client instance.
     *
     * @var HttpClient
     */
    protected HttpClient $httpClient;

    /**
     * Initialize or override the HTTP client.
     *
     * @param HttpClient|null $client
     *
     * @return self
     */
    public function setHttpClient(?HttpClient $client = null): self
    {
        $this->httpClient = $client ?? new HttpClient([
            'timeout' => 30,
            'verify'  => true,
        ]);

        return $this;
    }

    /**
     * Get the HTTP client, initializing if needed.
     *
     * @return HttpClient
     */
    protected function getHttpClient(): HttpClient
    {
        if (!isset($this->httpClient)) {
            $this->setHttpClient();
        }

        return $this->httpClient;
    }
}
