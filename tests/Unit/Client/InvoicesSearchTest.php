<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can search invoices', function () {
    $expectedResponse = $this->mockSearchInvoicesResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/invoicing/search-invoices?page=1&page_size=1&total_required=true';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->invoiceSearchParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(json_decode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
