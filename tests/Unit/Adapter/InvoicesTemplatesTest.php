<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create invoice template', function () {
    $expectedResponse = $this->mockCreateInvoiceTemplateResponse();

    $expectedParams = $this->mockCreateInvoiceTemplateParams();

    $expectedMethod = 'createInvoiceTemplate';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can list invoice templates', function () {
    $expectedResponse = $this->mockListInvoiceTemplateResponse();

    $expectedMethod = 'listInvoiceTemplates';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can delete an invoice template', function () {
    $expectedResponse = '';

    $expectedMethod = 'deleteInvoiceTemplate';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('TEMP-19V05281TU309413B'))->toBe($expectedResponse);
});

it('can update an invoice template', function () {
    $expectedResponse = $this->mockUpdateInvoiceTemplateResponse();

    $expectedParams = $this->mockUpdateInvoiceTemplateParams();

    $expectedMethod = 'updateInvoiceTemplate';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('TEMP-19V05281TU309413B', $expectedParams))->toBe($expectedResponse);
});

it('can get details for an invoice template', function () {
    $expectedResponse = $this->mockGetInvoiceTemplateResponse();

    $expectedMethod = 'showInvoiceTemplateDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('TEMP-19V05281TU309413B'))->toBe($expectedResponse);
});
