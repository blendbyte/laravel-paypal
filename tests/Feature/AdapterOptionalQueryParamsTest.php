<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

function lastUri(MockPayPalClient $mock): string
{
    return (string) $mock->lastRequest()->getUri();
}

it('refunds a capture in full without an amount', function () {
    $this->mock->addResponse(['id' => 'R-1', 'status' => 'COMPLETED']);

    $response = $this->client->refundCapturedPaymentInFull('C-1');

    expect($response)->toBe(['id' => 'R-1', 'status' => 'COMPLETED'])
        ->and(lastUri($this->mock))->toEndWith('/v2/payments/captures/C-1/refund')
        ->and((string) $this->mock->lastRequest()->getBody())->toBe('{}');
});

it('sends invoice id and note with a full refund', function () {
    $this->mock->addResponse(['id' => 'R-1']);

    $this->client->refundCapturedPaymentInFull('C-1', 'INV-1', 'Order cancelled');

    expect(json_decode((string) $this->mock->lastRequest()->getBody(), true))
        ->toBe(['invoice_id' => 'INV-1', 'note_to_payer' => 'Order cancelled']);
});

it('shows order details with and without fields', function () {
    $this->mock->addResponse(['id' => 'O-1']);
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->showOrderDetails('O-1');
    $default = lastUri($this->mock);
    $this->client->showOrderDetails('O-1', ['payment_source']);

    expect($default)->toEndWith('/v2/checkout/orders/O-1')
        ->and(lastUri($this->mock))->toEndWith('/v2/checkout/orders/O-1?fields=payment_source');
});

it('shows subscription details with and without fields', function () {
    $this->mock->addResponse(['id' => 'I-1']);
    $this->mock->addResponse(['id' => 'I-1']);

    $this->client->showSubscriptionDetails('I-1');
    $default = lastUri($this->mock);
    $this->client->showSubscriptionDetails('I-1', ['last_failed_payment', 'plan']);

    expect($default)->toEndWith('/v1/billing/subscriptions/I-1')
        ->and(lastUri($this->mock))->toEndWith('/v1/billing/subscriptions/I-1?fields=last_failed_payment,plan');
});

it('lists plans with the default query unchanged', function () {
    $this->mock->addResponse(['plans' => []]);

    $this->client->listPlans();

    expect(lastUri($this->mock))->toEndWith('/v1/billing/plans?page=1&page_size=20&total_required=true');
});

it('lists plans for a product and caps the page size at 20', function () {
    $this->mock->addResponse(['plans' => []]);

    $this->client->setPageSize(100)->listPlansForProduct('PROD-XXCD1234QWER65782');

    expect(lastUri($this->mock))->toEndWith('/v1/billing/plans?page=1&page_size=20&total_required=true&product_id=PROD-XXCD1234QWER65782');
});

it('lists webhook events with and without filters', function () {
    $this->mock->addResponse(['events' => []]);
    $this->mock->addResponse(['events' => []]);

    $this->client->listEvents();
    $default = lastUri($this->mock);
    $this->client->listEvents([
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'start_time' => '2026-09-01T00:00:00Z',
        'page_size' => 25,
    ]);

    expect($default)->toEndWith('/v1/notifications/webhooks-events')
        ->and(lastUri($this->mock))->toEndWith(
            '/v1/notifications/webhooks-events?event_type=PAYMENT.CAPTURE.COMPLETED&start_time=2026-09-01T00%3A00%3A00Z&page_size=25'
        );
});

it('ignores extra arguments to listPlans() as before', function () {
    // The README once documented listPlans(1, 30, true, [...]); PHP ignored those arguments.
    $this->mock->addResponse(['plans' => []]);

    $this->client->listPlans(1, 30, true, ['id', 'name']);

    expect(lastUri($this->mock))->toEndWith('/v1/billing/plans?page=1&page_size=20&total_required=true');
});
