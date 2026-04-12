<?php

/**
 * Architecture tests (E6) — enforce structural constraints on the package.
 *
 * Rules:
 *  1. Core infrastructure traits must not depend on Illuminate (they are Laravel-agnostic).
 *  2. PayPal API endpoint traits must not depend on Illuminate (pure API wrappers).
 *  3. Source files must use the correct Srmklive\PayPal namespace.
 *  4. Traits are declared as PHP traits, not accidentally as classes or interfaces.
 *  5. Service classes do not extend anything external (they compose via traits).
 */

// Rule 1 — Core infrastructure traits must remain Laravel-agnostic.
// PayPalHttpClient wraps Guzzle, PayPalExperienceContext builds request context,
// PayPalRequest handles credentials/config — none require the Laravel framework.
arch('core infrastructure traits are Laravel-agnostic')
    ->expect([
        'Srmklive\PayPal\Traits\PayPalHttpClient',
        'Srmklive\PayPal\Traits\PayPalExperienceContext',
        'Srmklive\PayPal\Traits\PayPalRequest',
    ])
    ->not->toUse('Illuminate');

// Rule 2 — PayPal API endpoint traits must remain Laravel-agnostic.
// These traits are pure PayPal REST API wrappers.
// Excludes Subscriptions\Helpers (uses Illuminate\Support\Str for random IDs).
arch('PayPal API endpoint traits are Laravel-agnostic')
    ->expect([
        'Srmklive\PayPal\Traits\PayPalAPI\BillingAgreements',
        'Srmklive\PayPal\Traits\PayPalAPI\BillingPlans',
        'Srmklive\PayPal\Traits\PayPalAPI\CatalogProducts',
        'Srmklive\PayPal\Traits\PayPalAPI\Disputes',
        'Srmklive\PayPal\Traits\PayPalAPI\DisputesActions',
        'Srmklive\PayPal\Traits\PayPalAPI\Identity',
        'Srmklive\PayPal\Traits\PayPalAPI\Invoices',
        'Srmklive\PayPal\Traits\PayPalAPI\InvoicesSearch',
        'Srmklive\PayPal\Traits\PayPalAPI\InvoicesTemplates',
        'Srmklive\PayPal\Traits\PayPalAPI\Orders',
        'Srmklive\PayPal\Traits\PayPalAPI\PartnerReferrals',
        'Srmklive\PayPal\Traits\PayPalAPI\PaymentAuthorizations',
        'Srmklive\PayPal\Traits\PayPalAPI\PaymentCaptures',
        'Srmklive\PayPal\Traits\PayPalAPI\PaymentExperienceWebProfiles',
        'Srmklive\PayPal\Traits\PayPalAPI\PaymentMethodsTokens',
        'Srmklive\PayPal\Traits\PayPalAPI\PaymentRefunds',
        'Srmklive\PayPal\Traits\PayPalAPI\Payouts',
        'Srmklive\PayPal\Traits\PayPalAPI\ReferencedPayouts',
        'Srmklive\PayPal\Traits\PayPalAPI\Reporting',
        'Srmklive\PayPal\Traits\PayPalAPI\Trackers',
        'Srmklive\PayPal\Traits\PayPalAPI\WebHooks',
        'Srmklive\PayPal\Traits\PayPalAPI\WebHooksEvents',
        'Srmklive\PayPal\Traits\PayPalAPI\WebHooksVerification',
    ])
    ->not->toUse('Illuminate');

// Rule 3 — Traits are declared as PHP traits, not accidentally as classes or interfaces.
arch('API traits are declared as traits')
    ->expect('Srmklive\PayPal\Traits')
    ->toBeTraits();

// Rule 4 — Services are declared as classes, not traits or interfaces.
arch('service classes are declared as classes')
    ->expect('Srmklive\PayPal\Services')
    ->toBeClasses();

// Rule 5 — The main PayPal service does not depend on Illuminate\Http\Request
//           (i.e. it must be usable outside Laravel HTTP context — IPN is the exception,
//            living in a separate trait that callers can opt into).
arch('PayPal service does not directly import Illuminate HTTP layer')
    ->expect('Srmklive\PayPal\Services\PayPal')
    ->not->toUse('Illuminate\Http');
