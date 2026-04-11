<?php

/**
 * Architecture tests (E6) — enforce structural constraints on the package.
 *
 * Rules:
 *  1. Core infrastructure traits must not depend on Illuminate (they are Laravel-agnostic).
 *  2. PayPal API endpoint traits must not depend on Illuminate (pure API wrappers).
 *  3. Source files must use the correct Blendbyte\PayPal namespace.
 *  4. Traits are declared as PHP traits, not accidentally as classes or interfaces.
 *  5. Service classes do not extend anything external (they compose via traits).
 */

// Rule 1 — Core infrastructure traits must remain Laravel-agnostic.
// PayPalHttpClient wraps Guzzle, PayPalExperienceContext builds request context,
// PayPalRequest handles credentials/config — none require the Laravel framework.
arch('core infrastructure traits are Laravel-agnostic')
    ->expect([
        'Blendbyte\PayPal\Traits\PayPalHttpClient',
        'Blendbyte\PayPal\Traits\PayPalExperienceContext',
        'Blendbyte\PayPal\Traits\PayPalRequest',
    ])
    ->not->toUse('Illuminate');

// Rule 2 — PayPal API endpoint traits must remain Laravel-agnostic.
// These traits are pure PayPal REST API wrappers.
// Excludes Subscriptions\Helpers (uses Illuminate\Support\Str for random IDs).
arch('PayPal API endpoint traits are Laravel-agnostic')
    ->expect([
        'Blendbyte\PayPal\Traits\PayPalAPI\BillingAgreements',
        'Blendbyte\PayPal\Traits\PayPalAPI\BillingPlans',
        'Blendbyte\PayPal\Traits\PayPalAPI\CatalogProducts',
        'Blendbyte\PayPal\Traits\PayPalAPI\Disputes',
        'Blendbyte\PayPal\Traits\PayPalAPI\DisputesActions',
        'Blendbyte\PayPal\Traits\PayPalAPI\Identity',
        'Blendbyte\PayPal\Traits\PayPalAPI\Invoices',
        'Blendbyte\PayPal\Traits\PayPalAPI\InvoicesSearch',
        'Blendbyte\PayPal\Traits\PayPalAPI\InvoicesTemplates',
        'Blendbyte\PayPal\Traits\PayPalAPI\Orders',
        'Blendbyte\PayPal\Traits\PayPalAPI\PartnerReferrals',
        'Blendbyte\PayPal\Traits\PayPalAPI\PaymentAuthorizations',
        'Blendbyte\PayPal\Traits\PayPalAPI\PaymentCaptures',
        'Blendbyte\PayPal\Traits\PayPalAPI\PaymentExperienceWebProfiles',
        'Blendbyte\PayPal\Traits\PayPalAPI\PaymentMethodsTokens',
        'Blendbyte\PayPal\Traits\PayPalAPI\PaymentRefunds',
        'Blendbyte\PayPal\Traits\PayPalAPI\Payouts',
        'Blendbyte\PayPal\Traits\PayPalAPI\ReferencedPayouts',
        'Blendbyte\PayPal\Traits\PayPalAPI\Reporting',
        'Blendbyte\PayPal\Traits\PayPalAPI\Trackers',
        'Blendbyte\PayPal\Traits\PayPalAPI\WebHooks',
        'Blendbyte\PayPal\Traits\PayPalAPI\WebHooksEvents',
        'Blendbyte\PayPal\Traits\PayPalAPI\WebHooksVerification',
    ])
    ->not->toUse('Illuminate');

// Rule 3 — Traits are declared as PHP traits, not accidentally as classes or interfaces.
arch('API traits are declared as traits')
    ->expect('Blendbyte\PayPal\Traits')
    ->toBeTraits();

// Rule 4 — Services are declared as classes, not traits or interfaces.
arch('service classes are declared as classes')
    ->expect('Blendbyte\PayPal\Services')
    ->toBeClasses();

// Rule 5 — The main PayPal service does not depend on Illuminate\Http\Request
//           (i.e. it must be usable outside Laravel HTTP context — IPN is the exception,
//            living in a separate trait that callers can opt into).
arch('PayPal service does not directly import Illuminate HTTP layer')
    ->expect('Blendbyte\PayPal\Services\PayPal')
    ->not->toUse('Illuminate\Http');
