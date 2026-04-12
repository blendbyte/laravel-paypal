<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can search invoices', function () {
    $expectedResponse = $this->mockSearchInvoicesResponse();

    $expectedMethod = 'searchInvoices';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(1, 1, true))->toBe($expectedResponse);
});

it('can search invoices with custom filters', function () {
    $expectedResponse = $this->mockSearchInvoicesResponse();

    $expectedMethod = 'searchInvoices';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->addInvoiceFilterByRecipientEmail('bill-me@example.com')
        ->addInvoiceFilterByCurrencyCode('USD')
        ->addInvoiceFilterByAmountRange(30, 50)
        ->{$expectedMethod}(1, 1, true))->toBe($expectedResponse);
});
