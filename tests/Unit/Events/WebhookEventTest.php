<?php

use Srmklive\PayPal\Events\WebhookEvent;

describe('WebhookEvent::fromArray', function () {
    it('maps all fields from a full payload', function () {
        $payload = [
            'id'            => '8PT597110X687430LKGECATA',
            'event_type'    => 'PAYMENT.CAPTURE.COMPLETED',
            'resource_type' => 'capture',
            'summary'       => 'Payment completed for $ 7.47 USD',
            'create_time'   => '2021-01-01T12:00:00Z',
            'resource'      => ['id' => 'CAP-123', 'amount' => ['value' => '7.47']],
        ];

        $event = WebhookEvent::fromArray($payload);

        expect($event->id)->toBe('8PT597110X687430LKGECATA');
        expect($event->eventType)->toBe('PAYMENT.CAPTURE.COMPLETED');
        expect($event->resourceType)->toBe('capture');
        expect($event->summary)->toBe('Payment completed for $ 7.47 USD');
        expect($event->createTime)->toBe('2021-01-01T12:00:00Z');
        expect($event->resource)->toBe(['id' => 'CAP-123', 'amount' => ['value' => '7.47']]);
        expect($event->rawPayload)->toBe($payload);
    });

    it('defaults missing fields to empty strings', function () {
        $event = WebhookEvent::fromArray([]);

        expect($event->id)->toBe('');
        expect($event->eventType)->toBe('');
        expect($event->resourceType)->toBe('');
        expect($event->summary)->toBe('');
        expect($event->createTime)->toBe('');
        expect($event->resource)->toBe([]);
    });

    it('defaults resource to empty array when absent', function () {
        $event = WebhookEvent::fromArray(['event_type' => 'PAYMENT.CAPTURE.COMPLETED']);

        expect($event->resource)->toBe([]);
    });

    it('defaults resource to empty array when not an array', function () {
        $event = WebhookEvent::fromArray(['resource' => 'invalid']);

        expect($event->resource)->toBe([]);
    });

    it('preserves the full rawPayload', function () {
        $payload = ['event_type' => 'BILLING.SUBSCRIPTION.CANCELLED', 'custom_field' => 'abc'];

        $event = WebhookEvent::fromArray($payload);

        expect($event->rawPayload)->toBe($payload);
    });
});

describe('WebhookEvent::fromRawBody', function () {
    it('parses a valid JSON body', function () {
        $body = json_encode([
            'id'         => 'EVT-001',
            'event_type' => 'PAYMENT.AUTHORIZATION.CREATED',
            'resource'   => ['id' => 'AUTH-123'],
        ]);

        $event = WebhookEvent::fromRawBody($body);

        expect($event->id)->toBe('EVT-001');
        expect($event->eventType)->toBe('PAYMENT.AUTHORIZATION.CREATED');
        expect($event->resource)->toBe(['id' => 'AUTH-123']);
    });

    it('returns an empty event for invalid JSON without throwing', function () {
        $event = WebhookEvent::fromRawBody('not-json-at-all');

        expect($event->id)->toBe('');
        expect($event->eventType)->toBe('');
        expect($event->resource)->toBe([]);
    });

    it('returns an empty event for an empty string', function () {
        $event = WebhookEvent::fromRawBody('');

        expect($event->eventType)->toBe('');
    });

    it('returns an empty event when JSON is not an object', function () {
        $event = WebhookEvent::fromRawBody('"just-a-string"');

        expect($event->eventType)->toBe('');
    });
});

describe('WebhookEvent::is', function () {
    it('returns true when the event type matches', function () {
        $event = WebhookEvent::fromArray(['event_type' => 'PAYMENT.CAPTURE.COMPLETED']);

        expect($event->is('PAYMENT.CAPTURE.COMPLETED'))->toBeTrue();
    });

    it('returns false when the event type does not match', function () {
        $event = WebhookEvent::fromArray(['event_type' => 'PAYMENT.CAPTURE.COMPLETED']);

        expect($event->is('BILLING.SUBSCRIPTION.CANCELLED'))->toBeFalse();
    });

    it('is case-sensitive', function () {
        $event = WebhookEvent::fromArray(['event_type' => 'PAYMENT.CAPTURE.COMPLETED']);

        expect($event->is('payment.capture.completed'))->toBeFalse();
    });
});
