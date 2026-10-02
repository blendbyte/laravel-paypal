<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

function searchFilters(MockPayPalClient $mock): array
{
    return json_decode((string) $mock->lastRequest()->getBody(), true);
}

it('sends whole-day UTC date-times for date-only payment and creation date ranges', function (string $type) {
    $this->mock->addResponse(['items' => []]);

    $this->client->addInvoiceFilterByDateRange('2024-01-01', '2024-06-30', $type)->searchInvoices();

    expect(searchFilters($this->mock)["{$type}_range"])->toBe([
        'start' => '2024-01-01T00:00:00Z',
        'end' => '2024-06-30T23:59:59Z',
    ]);
})->with(['payment_date', 'creation_date']);

it('keeps the time and converts to UTC for date-time inputs', function () {
    $this->mock->addResponse(['items' => []]);

    $this->client->addInvoiceFilterByDateRange('2024-01-01T10:30:00+02:00', '2024-01-02 08:00:00', 'payment_date')->searchInvoices();

    expect(searchFilters($this->mock)['payment_date_range'])->toBe([
        'start' => '2024-01-01T08:30:00Z',
        'end' => '2024-01-02T08:00:00Z',
    ]);
});

it('keeps plain dates for invoice and due date ranges', function (string $type) {
    $this->mock->addResponse(['items' => []]);

    $this->client->addInvoiceFilterByDateRange('2024-01-01', '2024-06-30', $type)->searchInvoices();

    expect(searchFilters($this->mock)["{$type}_range"])->toBe(['start' => '2024-01-01', 'end' => '2024-06-30']);
})->with(['invoice_date', 'due_date']);

it('accepts all invoice statuses defined by the API', function () {
    $this->mock->addResponse(['items' => []]);

    $statuses = ['AUTO_CANCELLED', 'PAID_EXTERNAL', 'REFUNDED_EXTERNAL', 'SHARED'];
    $this->client->addInvoiceFilterByInvoiceStatus($statuses)->searchInvoices();

    expect(searchFilters($this->mock)['status'])->toBe($statuses);
});

it('updates an invoice without notification parameters by default', function () {
    $this->mock->addResponse(['id' => 'INV2-1']);

    $this->client->updateInvoice('INV2-1', ['detail' => []]);

    expect((string) $this->mock->lastRequest()->getUri())->toEndWith('/v2/invoicing/invoices/INV2-1');
});

it('can update an invoice without notifying recipient or merchant', function () {
    $this->mock->addResponse(['id' => 'INV2-1']);

    $this->client->updateInvoice('INV2-1', ['detail' => []], false, false);

    expect((string) $this->mock->lastRequest()->getUri())
        ->toEndWith('/v2/invoicing/invoices/INV2-1?send_to_recipient=false&send_to_invoicer=false');
});
