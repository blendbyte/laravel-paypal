<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create invoice template', function () {
    $expectedResponse = $this->mockCreateInvoiceTemplateResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/invoicing/templates';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockCreateInvoiceTemplateParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'post');

    expect(json_decode($mockHttpClient->post($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can list invoice templates', function () {
    $expectedResponse = $this->mockListInvoiceTemplateResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/invoicing/templates';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(json_decode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can delete an invoice template', function () {
    $expectedResponse = '';

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'delete');

    expect(json_decode($mockHttpClient->delete($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can update an invoice template', function () {
    $expectedResponse = $this->mockUpdateInvoiceTemplateResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
        'json' => $this->mockUpdateInvoiceTemplateParams(),
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'put');

    expect(json_decode($mockHttpClient->put($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});

it('can get details for an invoice template', function () {
    $expectedResponse = $this->mockGetInvoiceTemplateResponse();

    $expectedEndpoint = 'https://api-m.sandbox.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B';
    $expectedParams = [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Bearer some-token',
        ],
    ];

    $mockHttpClient = $this->mock_http_request(json_encode($expectedResponse), $expectedEndpoint, $expectedParams, 'get');

    expect(json_decode($mockHttpClient->get($expectedEndpoint, $expectedParams)->getBody(), true))->toBe($expectedResponse);
});
