<?php

namespace Srmklive\PayPal\Events;

/**
 * Typed value object wrapping an incoming PayPal webhook payload.
 *
 * Construct via the static factories rather than the constructor directly:
 *
 *   $event = WebhookEvent::fromRawBody($request->getContent());
 *   $event = WebhookEvent::fromArray(json_decode($body, true));
 */
final class WebhookEvent
{
    /**
     * @param array<string, mixed> $resource   The event-specific resource object.
     * @param array<string, mixed> $rawPayload The full decoded payload.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $eventType,
        public readonly string $resourceType,
        public readonly string $summary,
        public readonly string $createTime,
        public readonly array $resource,
        public readonly array $rawPayload,
    ) {}

    /**
     * Build a WebhookEvent from a decoded payload array.
     *
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        /** @var array<string, mixed> $resource */
        $resource = is_array($payload['resource'] ?? null) ? $payload['resource'] : [];

        return new self(
            id: (string) ($payload['id'] ?? ''),
            eventType: (string) ($payload['event_type'] ?? ''),
            resourceType: (string) ($payload['resource_type'] ?? ''),
            summary: (string) ($payload['summary'] ?? ''),
            createTime: (string) ($payload['create_time'] ?? ''),
            resource: $resource,
            rawPayload: $payload,
        );
    }

    /**
     * Build a WebhookEvent from a raw JSON request body string.
     *
     * Invalid JSON produces an empty event rather than throwing.
     */
    public static function fromRawBody(string $rawBody): self
    {
        $decoded = json_decode($rawBody, true);

        return self::fromArray(is_array($decoded) ? $decoded : []);
    }

    /**
     * Return true if this event's type matches the given string.
     *
     * Useful for routing without repeated property access:
     *
     *   if ($event->is('PAYMENT.CAPTURE.COMPLETED')) { ... }
     */
    public function is(string $eventType): bool
    {
        return $this->eventType === $eventType;
    }
}
