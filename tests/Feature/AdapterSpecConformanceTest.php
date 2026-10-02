<?php

use Carbon\Carbon;
use Srmklive\PayPal\Builders\BillingPlanBuilder;
use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

afterEach(function () {
    Carbon::setTestNow();
});

it('encodes webhook event types as a JSON array for non-list input', function () {
    $this->mock->addResponse(['id' => 'WH-1']);

    $this->client->createWebHook('https://example.com/hook', [2 => 'PAYMENT.CAPTURE.COMPLETED', 5 => 'PAYMENT.CAPTURE.DENIED']);

    expect((string) $this->mock->lastRequest()->getBody())
        ->toContain('"event_types":[{"name":"PAYMENT.CAPTURE.COMPLETED"},{"name":"PAYMENT.CAPTURE.DENIED"}]');
});

it('orders trial cycles before the regular cycle in the plan builder', function () {
    $plan = BillingPlanBuilder::make()
        ->forProduct('PROD-1')
        ->named('Plan')
        ->monthly(20)
        ->trialMonthly(0)
        ->build();

    expect(array_column($plan['billing_cycles'], 'tenure_type'))->toBe(['TRIAL', 'REGULAR'])
        ->and(array_column($plan['billing_cycles'], 'sequence'))->toBe([1, 2]);
});

it('defaults subscription transactions to the last 30 days', function () {
    Carbon::setTestNow('2026-10-02 12:00:00');
    $this->mock->addResponse(['transactions' => []]);

    $this->client->listSubscriptionTransactions('I-1');

    expect((string) $this->mock->lastRequest()->getUri())
        ->toEndWith('/v1/billing/subscriptions/I-1/transactions?start_time=2026-09-02T12:00:00Z&end_time=2026-10-02T12:00:00Z');
});

it('keeps explicit subscription transaction dates', function () {
    $this->mock->addResponse(['transactions' => []]);

    $this->client->listSubscriptionTransactions('I-1', '2026-01-01', '2026-02-01');

    expect((string) $this->mock->lastRequest()->getUri())
        ->toEndWith('start_time=2026-01-01T00:00:00Z&end_time=2026-02-01T00:00:00Z');
});

it('lists disputes with the default page size and no filters', function () {
    $this->mock->addResponse(['items' => []]);

    $this->client->listDisputes();

    expect((string) $this->mock->lastRequest()->getUri())->toEndWith('/v1/customer/disputes?page_size=20');
});

it('lists disputes with filters and caps the page size at 50', function () {
    $this->mock->addResponse(['items' => []]);

    $this->client->setPageSize(100)->listDisputes([
        'dispute_state' => ['REQUIRED_ACTION', 'UNDER_PAYPAL_REVIEW'],
        'update_time_after' => '2026-09-01T00:00:00Z',
    ]);

    expect((string) $this->mock->lastRequest()->getUri())->toEndWith(
        '/v1/customer/disputes?page_size=50&dispute_state=REQUIRED_ACTION%2CUNDER_PAYPAL_REVIEW&update_time_after=2026-09-01T00%3A00%3A00Z'
    );
});

it('shows batch payout details without paging parameters by default', function () {
    $this->mock->addResponse(['batch_header' => []]);

    $this->client->showBatchPayoutDetails('PB-1');

    expect((string) $this->mock->lastRequest()->getUri())->toEndWith('/v1/payments/payouts/PB-1');
});

it('pages through batch payout items', function () {
    $this->mock->addResponse(['batch_header' => []]);

    $this->client->showBatchPayoutDetails('PB-1', 2, 100, true);

    expect((string) $this->mock->lastRequest()->getUri())
        ->toEndWith('/v1/payments/payouts/PB-1?page=2&page_size=100&total_required=true');
});

it('keeps the transaction detail window at exactly the requested days', function () {
    Carbon::setTestNow('2026-10-02 12:00:00');
    $this->mock->addResponse(['transaction_details' => []]);

    $this->client->getTransactionDetails('TX-1');

    $uri = urldecode((string) $this->mock->lastRequest()->getUri());
    expect($uri)->toContain('start_date=2026-09-01T12:00:00Z')->toContain('end_date=2026-10-02T12:00:00Z');
});

it('adds an idempotency key to single-step orders with a payment source', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->setPaymentSourceCard(['vault_id' => 'V-1'])
        ->createOrderWithPaymentSource(['intent' => 'CAPTURE', 'purchase_units' => []]);

    expect($this->mock->lastRequest()->getHeaderLine('PayPal-Request-Id'))
        ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/');
});

it('keeps an explicit idempotency key for single-step orders', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->setPaymentSourceCard(['vault_id' => 'V-1'])
        ->withIdempotencyKey('my-key')
        ->createOrderWithPaymentSource(['intent' => 'CAPTURE', 'purchase_units' => []]);

    expect($this->mock->lastRequest()->getHeaderLine('PayPal-Request-Id'))->toBe('my-key');
});

it('does not add an idempotency key to orders without a payment source', function () {
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->createOrderWithPaymentSource(['intent' => 'CAPTURE', 'purchase_units' => []]);

    expect($this->mock->lastRequest()->hasHeader('PayPal-Request-Id'))->toBeFalse();
});

it('adds an idempotency key when creating a web experience profile', function () {
    $this->mock->addResponse(['id' => 'XP-1']);

    $this->client->createWebExperienceProfile(['name' => 'Profile']);

    expect($this->mock->lastRequest()->getHeaderLine('PayPal-Request-Id'))->not->toBe('');
});
