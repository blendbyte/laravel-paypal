<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;
use GuzzleHttp\Utils;

uses(MockRequestPayloads::class);

it('can list transactions', function () {
    $expectedResponse = $this->mockListTransactionsResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/reporting/transactions?start_date=2014-07-01T00:00:00-0700&end_date=2014-07-30T23:59:59-0700&transaction_id=5TY05013RG002845M&fields=all&page_size=100&page=1';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');
    $mockResponse = $mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody();

    expect(Utils::jsonDecode($mockResponse, true))->toHaveKey('transaction_details');
});

it('can list balances', function () {
    $expectedResponse = $this->mockListBalancesResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v1/reporting/balances?currency_code=USD&as_of_time=2016-10-15T06:07:00-0700';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(Utils::jsonEncode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(Utils::jsonDecode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
