<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can generate unique invoice number', function () {
    $expectedResponse = $this->mockGenerateInvoiceNumberResponse();

    $expectedMethod = 'generateInvoiceNumber';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can create a draft invoice', function () {
    $expectedResponse = $this->mockCreateInvoicesResponse();

    $expectedParams = $this->createInvoiceParams();

    $expectedMethod = 'createInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can list current invoices', function () {
    $expectedResponse = $this->mockListInvoicesResponse();

    $expectedMethod = 'listInvoices';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can delete an invoice', function () {
    $expectedResponse = '';

    $expectedMethod = 'deleteInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('INV2-Z56S-5LLA-Q52L-CPZ5'))->toBe($expectedResponse);
});

it('can update an invoice', function () {
    $expectedResponse = $this->mockUpdateInvoicesResponse();

    $expectedParams = $this->updateInvoiceParams();

    $expectedMethod = 'updateInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('INV2-Z56S-5LLA-Q52L-CPZ5', $expectedParams))->toBe($expectedResponse);
});

it('can show details for an invoice', function () {
    $expectedResponse = $this->mockGetInvoicesResponse();

    $expectedMethod = 'showInvoiceDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('INV2-Z56S-5LLA-Q52L-CPZ5'))->toBe($expectedResponse);
});

it('can cancel an invoice', function () {
    $expectedResponse = '';

    $expectedParams = $this->cancelInvoiceParams();

    $expectedMethod = 'cancelInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(
        'INV2-Z56S-5LLA-Q52L-CPZ5',
        'Reminder: Payment due for the invoice #ABC-123',
        'Please pay before the due date to avoid incurring late payment charges which will be adjusted in the next bill generated.',
        true,
        true,
        [
            'customer-a@example.com',
            'customer@example.com',
        ]
    ))->toBe($expectedResponse);
});

it('can generate qr code for invoice', function () {
    $expectedResponse = $this->mockGenerateInvoiceQRCodeResponse();

    $expectedMethod = 'generateQRCodeInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('INV2-Z56S-5LLA-Q52L-CPZ5', 400, 400))->toBe($expectedResponse);
});

it('can register payment for invoice', function () {
    $expectedResponse = $this->mockInvoiceRegisterPaymentResponse();

    $expectedMethod = 'registerPaymentInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('INV2-Z56S-5LLA-Q52L-CPZ5', '2018-05-01', 'BANK_TRANSFER', 10.00))->toBe($expectedResponse);
});

it('can delete payment for invoice', function () {
    $expectedResponse = '';

    $expectedMethod = 'deleteExternalPaymentInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('INV2-Z56S-5LLA-Q52L-CPZ5', 'EXTR-86F38350LX4353815'))->toBe($expectedResponse);
});

it('can refund payment for invoice', function () {
    $expectedResponse = $this->mockInvoiceRefundPaymentResponse();

    $expectedMethod = 'refundInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('INV2-Z56S-5LLA-Q52L-CPZ5', '2018-05-01', 'BANK_TRANSFER', 5.00))->toBe($expectedResponse);
});

it('can delete refund for invoice', function () {
    $expectedResponse = '';

    $expectedMethod = 'deleteRefundInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('INV2-Z56S-5LLA-Q52L-CPZ5', 'EXTR-2LG703375E477444T'))->toBe($expectedResponse);
});

it('can send an invoice', function () {
    $expectedResponse = '';

    $expectedMethod = 'sendInvoice';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(
        'INV2-Z56S-5LLA-Q52L-CPZ5',
        'Reminder: Payment due for the invoice #ABC-123',
        'Please pay before the due date to avoid incurring late payment charges which will be adjusted in the next bill generated.',
        true,
        true,
        [
            'customer-a@example.com',
            'customer@example.com',
        ]
    ))->toBe($expectedResponse);
});

it('can send reminder for an invoice', function () {
    $expectedResponse = '';

    $expectedMethod = 'sendInvoiceReminder';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(
        'INV2-Z56S-5LLA-Q52L-CPZ5',
        'Reminder: Payment due for the invoice #ABC-123',
        'Please pay before the due date to avoid incurring late payment charges which will be adjusted in the next bill generated.',
        true,
        true,
        [
            'customer-a@example.com',
            'customer@example.com',
        ]
    ))->toBe($expectedResponse);
});
