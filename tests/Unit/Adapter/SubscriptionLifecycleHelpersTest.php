<?php

use Srmklive\PayPal\Testing\MockPayPalClient;
use Srmklive\PayPal\Tests\MockResponsePayloads;

uses(MockResponsePayloads::class);

describe('reactivateSubscription', function () {
    it('calls activateSubscription with default reason', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(false, 204);

        $provider = $mock->mockProvider();
        $provider->reactivateSubscription('I-BW452GLLEP1G');

        expect($mock->requestCount())->toBe(1);
        expect($mock->lastRequest()->getMethod())->toBe('POST');
        expect((string) $mock->lastRequest()->getUri())->toContain('v1/billing/subscriptions/I-BW452GLLEP1G/activate');

        $body = json_decode((string) $mock->lastRequest()->getBody(), true);
        expect($body['reason'])->toBe('Reactivating subscription');
    });

    it('calls activateSubscription with a custom reason', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(false, 204);

        $provider = $mock->mockProvider();
        $provider->reactivateSubscription('I-BW452GLLEP1G', 'Customer requested resume');

        $body = json_decode((string) $mock->lastRequest()->getBody(), true);
        expect($body['reason'])->toBe('Customer requested resume');
    });
});

describe('isSubscriptionActive', function () {
    it('returns true when subscription status is ACTIVE', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'I-BW452GLLEP1G', 'status' => 'ACTIVE']);

        $provider = $mock->mockProvider();

        expect($provider->isSubscriptionActive('I-BW452GLLEP1G'))->toBeTrue();
        expect($mock->lastRequest()->getMethod())->toBe('GET');
        expect((string) $mock->lastRequest()->getUri())->toContain('v1/billing/subscriptions/I-BW452GLLEP1G');
    });

    it('returns false when subscription status is SUSPENDED', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'I-BW452GLLEP1G', 'status' => 'SUSPENDED']);

        $provider = $mock->mockProvider();

        expect($provider->isSubscriptionActive('I-BW452GLLEP1G'))->toBeFalse();
    });

    it('returns false when subscription status is CANCELLED', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'I-BW452GLLEP1G', 'status' => 'CANCELLED']);

        $provider = $mock->mockProvider();

        expect($provider->isSubscriptionActive('I-BW452GLLEP1G'))->toBeFalse();
    });

    it('returns false when subscription status is EXPIRED', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['id' => 'I-BW452GLLEP1G', 'status' => 'EXPIRED']);

        $provider = $mock->mockProvider();

        expect($provider->isSubscriptionActive('I-BW452GLLEP1G'))->toBeFalse();
    });

    it('returns false when the API returns an error', function () {
        $mock = new MockPayPalClient();
        $mock->addResponse(['error' => ['name' => 'RESOURCE_NOT_FOUND']], 404);

        $provider = $mock->mockProvider();

        expect($provider->isSubscriptionActive('I-INVALID'))->toBeFalse();
    });
});
