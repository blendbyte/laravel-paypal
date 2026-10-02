<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

function lastJsonBody(MockPayPalClient $mock): string
{
    return (string) $mock->lastRequest()->getBody();
}

it('sends send_to_recipient false instead of dropping it', function () {
    $this->mock->addResponse(false, 204);
    $this->client->sendInvoiceReminder('INV2-1', '', '', false);

    expect(json_decode(lastJsonBody($this->mock), true))
        ->toBe(['send_to_invoicer' => false, 'send_to_recipient' => false]);
});

it('always sends a JSON object with both notification flags', function () {
    $this->mock->addResponse(false, 204);
    $this->client->cancelInvoice('INV2-1');

    expect(lastJsonBody($this->mock))->toBe('{"send_to_invoicer":false,"send_to_recipient":true}');
});

it('includes subject, note and additional recipients as a JSON array when given', function () {
    $this->mock->addResponse(false, 204);
    $this->client->cancelInvoice('INV2-1', 'Subject', 'Note', true, true, [3 => 'user@example.com']);

    expect(json_decode(lastJsonBody($this->mock), true))->toBe([
        'send_to_invoicer' => true,
        'send_to_recipient' => true,
        'subject' => 'Subject',
        'note' => 'Note',
        'additional_recipients' => ['user@example.com'],
    ]);
});

it('decodes the sendInvoice response', function () {
    $link = ['href' => 'https://www.sandbox.paypal.com/invoice/p/#INV2-1', 'rel' => 'payer-view', 'method' => 'GET'];
    $this->mock->addResponse($link);

    expect($this->client->sendInvoice('INV2-1'))->toBe($link);
});
