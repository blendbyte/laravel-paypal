<img alt="banner-1" src="https://github.com/user-attachments/assets/d1fa2da5-51c7-4528-a969-514664a9c673" />

# Laravel PayPal

[![Latest Version on Packagist](https://img.shields.io/packagist/v/srmklive/paypal.svg?style=flat-square)](https://packagist.org/packages/srmklive/paypal)
[![Total Downloads](https://img.shields.io/packagist/dt/srmklive/paypal.svg?style=flat-square)](https://packagist.org/packages/srmklive/paypal)
[![Tests](https://github.com/blendbyte/laravel-paypal/actions/workflows/tests.yml/badge.svg)](https://github.com/blendbyte/laravel-paypal/actions/workflows/tests.yml)
[![Coverage](https://codecov.io/gh/blendbyte/laravel-paypal/branch/main/graph/badge.svg)](https://codecov.io/gh/blendbyte/laravel-paypal)
[![Static Analysis](https://github.com/blendbyte/laravel-paypal/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/blendbyte/laravel-paypal/actions/workflows/static-analysis.yml)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A PayPal REST API package for Laravel, also usable as a standalone PHP client without any framework.

**Supports:** PHP 8.2–8.5 · Laravel 12 / 13

> **Disclaimer:** This package is an independent community project and is not affiliated with, endorsed by, or supported by PayPal, Inc. "PayPal" is a registered trademark of PayPal, Inc. Use this package at your own risk; no warranty is provided beyond what the MIT license covers.

---

- [Version Compatibility](#version-compatibility)
- [Moving to Orders v2 (v1 Payments API sunset Jan 2027)](#moving-to-orders-v2-v1-payments-api-sunset-jan-2027)
- [Installation](#installation)
- [Standalone Usage (without Laravel)](#standalone-usage-without-laravel)
- [Configuration](#configuration)
- [Usage](#usage)
- [PayPal Fastlane](#paypal-fastlane)
- [Pay Upon Invoice (Buy Now, Pay Later — DE/AT)](#pay-upon-invoice-buy-now-pay-later--deat)
- [Subscription Helpers](#subscription-helpers)
- [Billing Plans](#billing-plans)
  - [BillingPlanBuilder](#billingplanbuilder)
- [Catalog Products](#catalog-products)
- [Orders](#orders)
- [Payments](#payments)
- [Payouts](#payouts)
- [Referenced Payouts](#referenced-payouts)
- [Reference Transactions (Billing Agreements)](#reference-transactions-billing-agreements)
- [Invoices](#invoices)
- [Invoice Search](#invoice-search)
- [Invoice Templates](#invoice-templates)
- [Subscriptions](#subscriptions)
- [Disputes](#disputes)
- [Dispute Actions](#dispute-actions)
- [Trackers](#trackers)
- [Webhooks](#webhooks)
- [Payment Method Tokens](#payment-method-tokens)
- [Reporting](#reporting)
- [Identity](#identity)
- [Partner Referrals](#partner-referrals)
- [Payment Experience](#payment-experience)

---

## Version Compatibility

| Version | PayPal API | PayPal Deprecation | PHP | Laravel | Maintained |
|---|---|---|---|---|---|
| v1.0 | Classic NVP/SOAP (Express Checkout, Adaptive Payments) | Deprecated since 2017, no firm sunset date | 5.6+ | 5.1+ | ❌ |
| v2.0 | REST v1 Payments + v2 Orders | `/v1/payments` sunset Jan 2027 | 7.2+ | 6+ | ❌ |
| v3.0 | REST v2 Orders + v2 Subscriptions | Current | 7.4+ | 6–12 | ❌ |
| **v3.1** | **REST v2 Orders + v2 Subscriptions** | **Current** | **8.2+** | **12–13** | **✅** |

### What's new in v3.1

- **PHP 8.2+** and **Laravel 12 / 13** required
- **Standalone usage** — no Laravel dependency required, pass credentials directly
- **PSR-18 HTTP client** — swap Guzzle for any compliant client via `setClient()`
- **Configurable timeouts and retries** — `timeout`, `connect_timeout`, `max_retries` in config
- **Exception-based error handling** — opt in with `withExceptions()` for `PayPalApiException`
- **Local webhook verification** — `verifyWebHookLocally()` with in-memory cert caching, no API roundtrip
- **PayPal Fastlane** — `generateClientToken()` for one-click guest checkout
- **Payment Method Tokens** — full Vault v3 API (setup tokens, permanent tokens, Apple Pay, Google Pay)
- **`getCaptureIdFromOrder()`** — extract capture/transaction ID from order responses
- **Bug fixes** — float precision, URL encoding, null guards, invoice date normalization
- **100% test coverage** with Pest v3/v4 and PHPStan level 7

---

## Moving to Orders v2 (v1 Payments API sunset Jan 2027)

PayPal is sunsetting the v1 Payments REST API (`/v1/payments/payment`) in **January 2027**. If your integration uses the old create-payment → redirect → execute-payment flow, or the classic Billing Agreements API (`/v1/billing-agreements/`), you need to migrate before then.

> **This package already uses Orders v2 and Subscriptions v2 throughout.** The migration notes below are for callers who built custom flows against the legacy endpoints or who were previously using the Express Checkout helpers from older versions.

### Checkout: redirect-based payment flow

| Old (v1 Payments — being sunset) | New (Orders v2) |
|---|---|
| `POST /v1/payments/payment` → redirect → `POST /v1/payments/payment/{id}/execute` | `createOrder()` → redirect → `capturePaymentOrder()` |

```php
// 1. Create the order and redirect the buyer
$order = $provider->createOrder([
    'intent' => 'CAPTURE',
    'purchase_units' => [
        ['amount' => ['currency_code' => 'USD', 'value' => '49.99']],
    ],
    'payment_source' => [
        'paypal' => [
            'experience_context' => [
                'return_url' => 'https://example.com/paypal/return',
                'cancel_url' => 'https://example.com/paypal/cancel',
            ],
        ],
    ],
]);

// Redirect the buyer to: $order['links'][href where rel === 'payer-action']

// 2. After the buyer approves, capture the payment
$capture = $provider->capturePaymentOrder($order['id']);
$captureId = $provider->getCaptureIdFromOrder($capture); // store this
```

### Recurring billing: Billing Agreements → Subscriptions

| Old (v1 Billing Agreements — being sunset) | New (Subscriptions v2) |
|---|---|
| `createBillingAgreementToken()` → `createBillingAgreement()` | `addProductById()` → `addBillingPlanById()` → `setupSubscription()` |

```php
// New subscriptions flow
$response = $provider->addProductById('PROD-XYAB12ABSB7868434')
    ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
    ->setReturnAndCancelUrl('https://example.com/success', 'https://example.com/cancel')
    ->setupSubscription('John Doe', 'john@example.com');

// Redirect the buyer to: $response['links'][href where rel === 'approve']
```

See the [Subscription Helpers](#subscription-helpers) section for creating plans programmatically.

---

## Installation

```bash
composer require srmklive/paypal
```

Publish the config file:

```bash
php artisan vendor:publish --provider "Srmklive\PayPal\Providers\PayPalServiceProvider"
```

---

## Standalone Usage (without Laravel)

The package has no hard dependency on Laravel — you can use it in any PHP project:

```bash
composer require srmklive/paypal
```

Instantiate the client and pass your credentials directly via `setApiCredentials()`. No service provider or `.env` file needed:

```php
use Srmklive\PayPal\Services\PayPal as PayPalClient;

$provider = new PayPalClient;

$provider->setApiCredentials([
    'mode' => 'sandbox', // or 'live'
    'sandbox' => [
        'client_id'     => 'YOUR_SANDBOX_CLIENT_ID',
        'client_secret' => 'YOUR_SANDBOX_CLIENT_SECRET',
        'app_id'        => 'APP-80W284485P519543T',
    ],
    'live' => [
        'client_id'     => 'YOUR_LIVE_CLIENT_ID',
        'client_secret' => 'YOUR_LIVE_CLIENT_SECRET',
        'app_id'        => 'YOUR_LIVE_APP_ID',
    ],
    'payment_action' => 'Sale',
    'currency'       => 'USD',
    'notify_url'     => '',
    'locale'         => 'en_US',
    'validate_ssl'   => true,
]);

$provider->getAccessToken();

// All API methods are now available
$order = $provider->createOrder([...]);
```

The facade and `php artisan vendor:publish` are Laravel-only conveniences; everything else works identically.

---

## Configuration

Add to your `.env`:

```env
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=
PAYPAL_SANDBOX_CLIENT_SECRET=
PAYPAL_LIVE_CLIENT_ID=
PAYPAL_LIVE_CLIENT_SECRET=
PAYPAL_LIVE_APP_ID=

# Optional — shown with defaults
PAYPAL_TIMEOUT=30
PAYPAL_CONNECT_TIMEOUT=10
PAYPAL_MAX_RETRIES=2
```

The published `config/paypal.php`:

```php
return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'), // 'sandbox' or 'live'
    'sandbox' => [
        'client_id'     => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
        'app_id'        => 'APP-80W284485P519543T',
    ],
    'live' => [
        'client_id'     => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
        'app_id'        => env('PAYPAL_LIVE_APP_ID', ''),
    ],
    'payment_action'  => env('PAYPAL_PAYMENT_ACTION', 'Sale'), // 'Sale', 'Authorization', or 'Order'
    'currency'        => env('PAYPAL_CURRENCY', 'USD'),
    'notify_url'      => env('PAYPAL_NOTIFY_URL', ''),
    'locale'          => env('PAYPAL_LOCALE', 'en_US'),
    'validate_ssl'    => env('PAYPAL_VALIDATE_SSL', true),
    'timeout'         => env('PAYPAL_TIMEOUT', 30),         // total request timeout (seconds)
    'connect_timeout' => env('PAYPAL_CONNECT_TIMEOUT', 10), // connection timeout (seconds)
    'max_retries'     => env('PAYPAL_MAX_RETRIES', 2),      // retries on 5xx / 429 / network errors (0 to disable)
];
```

---

## Usage

### Initialization

```php
use Srmklive\PayPal\Services\PayPal as PayPalClient;

$provider = new PayPalClient;

// Or via facade
$provider = \PayPal::setProvider();
```

### Custom HTTP Client (PSR-18)

`setClient()` accepts any [PSR-18](https://www.php-fig.org/psr/psr-18/) `ClientInterface`, so you can swap out Guzzle for Symfony HttpClient, Buzz, or any other compliant implementation:

```php
use Symfony\Component\HttpClient\Psr18Client;

$provider->setClient(new Psr18Client());
```

Pass `null` (or call with no argument) to restore the default Guzzle client with the configured timeout and retry middleware.

> **Note:** The built-in retry middleware runs only on the default Guzzle client. When you inject a custom client, handle retries in that client's own middleware stack.

### Retry Behaviour

The default Guzzle client automatically retries failed requests up to `max_retries` times (default: 2) for:

- **5xx server errors** — PayPal-side failures (500, 502, 503, …)
- **429 Too Many Requests** — rate-limit responses; the `Retry-After` header is read and honoured when present
- **Network/connection errors** — DNS failures, connection refused, etc.

The delay between attempts uses exponential backoff (500 ms → 1 s → 2 s → 4 s, capped at 8 s) unless a `Retry-After` header overrides it. Set `max_retries` to `0` to disable retries entirely.

---

### Override Configuration at Runtime

```php
$provider->setApiCredentials([
    'mode' => 'live',
    'live' => [
        'client_id'     => 'PAYPAL_LIVE_CLIENT_ID',
        'client_secret' => 'PAYPAL_LIVE_CLIENT_SECRET',
        'app_id'        => 'PAYPAL_LIVE_APP_ID',
    ],
    'payment_action' => 'Sale',
    'currency'       => 'USD',
    'notify_url'     => 'https://your-site.com/paypal/notify',
    'locale'         => 'en_US',
    'validate_ssl'   => true,
]);
```

### Get Access Token

Call this before any API method:

```php
$provider->getAccessToken();
```

### Set Currency

```php
$provider->setCurrency('EUR');
```

### Partner Attribution ID (BN code)

PayPal uses the `PayPal-Partner-Attribution-Id` header to attribute transactions to a partner or platform. Set it once after initialisation — it persists for the lifetime of the provider instance:

```php
$provider->setPartnerAttributionId('YourPlatform_SP');
```

All subsequent API calls will include the header automatically.

### Error Handling

By default, API errors are returned as an array with an `error` key:

```php
$response = $provider->showOrderDetails('bad-id');

if (isset($response['error'])) {
    // $response['error'] is the decoded PayPal error object or a plain string
}
```

Opt in to exceptions with `withExceptions()`. All API errors will then throw `PayPalApiException` instead:

```php
use Srmklive\PayPal\Exceptions\PayPalApiException;

$provider->withExceptions();

try {
    $order = $provider->showOrderDetails('bad-id');
} catch (PayPalApiException $e) {
    $e->getHttpStatus();    // HTTP status code: 400, 401, 404, 422, 500, etc. (0 for network errors)
    $e->getMessage();       // JSON-encoded error string
    $e->getPayPalError();   // decoded array (e.g. ['name' => 'RESOURCE_NOT_FOUND', ...])
                            // or a plain string for non-JSON errors
}
```

Call `withoutExceptions()` to revert to silent mode. Both methods are fluent.

---

## PayPal Fastlane

[PayPal Fastlane](https://developer.paypal.com/docs/checkout/fastlane/) is a one-click guest checkout experience that pre-fills shipping and payment details for returning PayPal customers, typically delivering ~50% higher conversion on guest checkout flows.

**Server role:** generate a client token and handle Orders v2 create/capture. The Fastlane UI is rendered entirely by the PayPal JS SDK on the client.

### 1. Generate a client token (server-side)

```php
$provider->getAccessToken();

$result = $provider->generateClientToken();
// $result['client_token'] — pass this to your frontend
```

### 2. Initialise Fastlane (client-side)

```html
<script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&components=fastlane"></script>
<script>
const { Fastlane } = await paypal.Fastlane({ clientToken: '<?= $result["client_token"] ?>' });
const { selectionChanged, selectedCard } = await Fastlane.identity.lookupCustomerByEmail(email);
// render Fastlane.FastlaneWatermarkComponent(), Fastlane.FastlaneCardComponent(), etc.
</script>
```

### 3. Create & capture the order (server-side)

```php
// Create
$order = $provider->createOrder([
    'intent' => 'CAPTURE',
    'purchase_units' => [
        ['amount' => ['currency_code' => 'USD', 'value' => '49.99']],
    ],
    'payment_source' => [
        'card' => [
            'single_use_token' => $singleUseToken, // from Fastlane.FastlaneCardComponent
        ],
    ],
]);

// Capture
$capture = $provider->capturePaymentOrder($order['id']);

// Extract the transaction/capture ID
$captureId = $provider->getCaptureIdFromOrder($capture);
```

---

## Pay Upon Invoice (Buy Now, Pay Later — DE/AT)

[Pay Upon Invoice](https://developer.paypal.com/docs/checkout/pay-upon-invoice/) (Rechnungskauf) lets buyers in Germany and Austria pay after receiving goods. PayPal collects the payment and the merchant is paid upfront.

**Requirements:** DE/AT merchant account, buyer name, email, date of birth, phone number, and billing address.

```php
$provider->getAccessToken();

$provider->setPaymentSourcePayUponInvoice([
    'name'       => ['given_name' => 'John', 'surname' => 'Doe'],
    'email'      => 'john.doe@example.com',
    'birth_date' => '1990-01-01',
    'phone'      => ['country_code' => '49', 'national_number' => '1234567890'],
    'billing_address' => [
        'address_line_1' => 'Hauptstraße 1',
        'admin_area_2'   => 'Berlin',
        'postal_code'    => '10115',
        'country_code'   => 'DE',
    ],
    'experience_context' => [
        'locale'     => 'de-DE',
        'return_url' => 'https://example.com/paypal-success',
        'cancel_url' => 'https://example.com/paypal-cancel',
    ],
]);

$order = $provider->createOrderWithPaymentSource([
    'intent'         => 'CAPTURE',
    'purchase_units' => [
        ['amount' => ['currency_code' => 'EUR', 'value' => '99.00']],
    ],
]);

$capture = $provider->capturePaymentOrder($order['id']);
```

---

## Subscription Helpers

A fluent helper API for creating subscriptions without manually building plan/product payloads.

> `addPlanTrialPricing()` and `setReturnAndCancelUrl()` are optional. Return/cancel URLs require real domains (not `localhost`).

### Daily Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
    ->addPlanTrialPricing('DAY', 7)
    ->addDailyPlan('Demo Plan', 'Demo Plan', 1.50)
    ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
    ->setupSubscription('John Doe', 'john@example.com', '2025-01-01');
```

### Weekly / Monthly / Annual Subscription

```php
// Weekly
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
    ->addPlanTrialPricing('DAY', 7)
    ->addWeeklyPlan('Demo Plan', 'Demo Plan', 30)
    ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
    ->setupSubscription('John Doe', 'john@example.com', '2025-01-01');

// Monthly
$response = $provider->addProduct(...)->addMonthlyPlan('Demo Plan', 'Demo Plan', 100)->...->setupSubscription(...);

// Annual
$response = $provider->addProduct(...)->addAnnualPlan('Demo Plan', 'Demo Plan', 600)->...->setupSubscription(...);
```

### Custom Interval

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
    ->addCustomPlan('Demo Plan', 'Demo Plan', 150, 'MONTH', 3)
    ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
    ->setupSubscription('John Doe', 'john@example.com', '2025-01-01');
```

### Use Existing Product & Billing Plan

```php
$response = $provider->addProductById('PROD-XYAB12ABSB7868434')
    ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
    ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
    ->setupSubscription('John Doe', 'john@example.com', '2025-01-01');
```

### Additional Options

```php
// Setup fee
$provider->addSetupFee(9.99)->addProductById(...)->...->setupSubscription(...);

// Shipping address
$provider->addShippingAddress('John Doe', '123 Main St', 'Suite 1', 'Austin', 'TX', 78701, 'US')
    ->addProductById(...)->...->setupSubscription(...);

// Payment failure threshold
$provider->addPaymentFailureThreshold(5)->addProductById(...)->...->setupSubscription(...);
```

### Update Pricing Schemes for a Billing Plan

```php
$response = $provider->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
    ->addPricingScheme('DAY', 7, 0, true)
    ->addPricingScheme('MONTH', 1, 100)
    ->processBillingPlanPricingUpdates();
```

---

## Billing Plans

### BillingPlanBuilder

Building a billing plan payload by hand is error-prone — cycles need correct sequences, prices must be strings, and the nesting is deep. `BillingPlanBuilder` handles all of that:

```php
use Srmklive\PayPal\Builders\BillingPlanBuilder;

$response = BillingPlanBuilder::make()
    ->forProduct('PROD-XXCD1234QWER65782')
    ->named('Premium Plan', 'Monthly premium access')
    ->monthly(9.99)
    ->create($provider);
```

With a trial period and setup fee:

```php
$response = BillingPlanBuilder::make()
    ->forProduct('PROD-XXCD1234QWER65782')
    ->named('Video Streaming Plan', 'Video Streaming Service basic plan')
    ->trialMonthly(3.00, totalCycles: 2)   // $3/mo for 2 months
    ->trialMonthly(6.00, totalCycles: 3)   // $6/mo for 3 months
    ->monthly(10.00, totalCycles: 12)      // $10/mo for 12 months
    ->withSetupFee(10.00)
    ->withTax(10.0)
    ->create($provider);
```

Cycles are sequenced automatically in the order they are added. Use `build()` instead of `create()` to get the raw array without making an API call:

```php
$payload = BillingPlanBuilder::make()
    ->forProduct('PROD-XXCD1234QWER65782')
    ->named('Annual Plan')
    ->annual(99.00)
    ->withCurrency('EUR')
    ->withFailureThreshold(5)
    ->build(); // returns array<string, mixed>

$provider->createPlan($payload);
```

**Available cycle methods:**

| Method | Interval | Tenure |
|---|---|---|
| `daily(price, totalCycles)` | DAY / 1 | REGULAR |
| `weekly(price, totalCycles)` | WEEK / 1 | REGULAR |
| `monthly(price, totalCycles)` | MONTH / 1 | REGULAR |
| `annual(price, totalCycles)` | YEAR / 1 | REGULAR |
| `trialDaily(price, totalCycles)` | DAY / 1 | TRIAL |
| `trialWeekly(price, totalCycles)` | WEEK / 1 | TRIAL |
| `trialMonthly(price, totalCycles)` | MONTH / 1 | TRIAL |
| `trialAnnual(price, totalCycles)` | YEAR / 1 | TRIAL |
| `regularCycle(unit, count, price, totalCycles)` | custom | REGULAR |
| `trialCycle(unit, count, price, totalCycles)` | custom | TRIAL |

`totalCycles: 0` means the cycle repeats indefinitely.

### Raw API

```php
// List (page, count, show_total, fields)
$plans = $provider->listPlans();
$plans = $provider->listPlans(1, 30, true, ['id', 'name', 'description']);

// Create
$plan = $provider->createPlan($data);

// Update
$provider->updatePlan('P-7GL4271244454362WXNWU5NQ', [
    ['op' => 'replace', 'path' => '/payment_preferences/payment_failure_threshold', 'value' => 7],
]);

// Show / Activate / Deactivate
$plan = $provider->showPlanDetails('P-7GL4271244454362WXNWU5NQ');
$provider->activatePlan('P-7GL4271244454362WXNWU5NQ');
$provider->deactivatePlan('P-7GL4271244454362WXNWU5NQ');

// Update pricing
$provider->updatePlanPricing('P-7GL4271244454362WXNWU5NQ', $pricingData);
```

---

## Catalog Products

```php
$products = $provider->listProducts();
$products = $provider->listProducts(1, 30, true);

$product = $provider->createProduct($data, 'create-product-'.time());

$provider->updateProduct('72255d4849af8ed6e0df1173', [
    ['op' => 'replace', 'path' => '/description', 'value' => 'Updated description'],
]);

$product = $provider->showProductDetails('72255d4849af8ed6e0df1173');
```

---

## Orders

```php
// Create
$order = $provider->createOrder([
    'intent' => 'CAPTURE',
    'purchase_units' => [
        ['amount' => ['currency_code' => 'USD', 'value' => '100.00']],
    ],
]);

// Update, show, authorize
$provider->updateOrder('5O190127TN364715T', $patchData);
$order = $provider->showOrderDetails('5O190127TN364715T');
$provider->authorizePaymentOrder('5O190127TN364715T');

// Capture — and extract the capture/transaction ID from the response
$capture = $provider->capturePaymentOrder($order['id']);
$captureId = $provider->getCaptureIdFromOrder($capture);
// $captureId is the value you store in your database and use for refunds,
// dispute lookups, and shipment tracking (see Trackers section).
```

---

## Payments

### Authorizations

```php
$provider->showAuthorizedPaymentDetails('0VF52814937998046');
$provider->captureAuthorizedPayment('0VF52814937998046', 'INVOICE-123', 10.99, 'Payment note');
$provider->reAuthorizeAuthorizedPayment('0VF52814937998046', 10.99);
$provider->voidAuthorizedPayment('0VF52814937998046');
```

### Captures & Refunds

```php
$provider->showCapturedPaymentDetails('2GG279541U471931P');
$provider->refundCapturedPayment('2GG279541U471931P', 'INVOICE-123', 10.99, 'Defective product');
$provider->showRefundDetails('1JU08902781691411');
```

---

## Payouts

```php
$provider->createBatchPayout($data);
$provider->showBatchPayoutDetails('FYXMPQTX4JC9N');
$provider->showPayoutItemDetails('8AELMXH8UB2P8');
$provider->cancelUnclaimedPayoutItem('8AELMXH8UB2P8');
```

---

## Referenced Payouts

```php
// Create batch
$provider->createReferencedBatchPayout([
    'referenced_payouts' => [
        ['reference_id' => '2KP03934U4415543C', 'reference_type' => 'TRANSACTION_ID'],
    ],
], 'some-request-id', 'some-attribution-id');

$provider->listItemsReferencedInBatchPayout('KHbwO28lWlXwi2IlToJ2IYNG4juFv6kpbFx4J9oQ5Hb24RSp96Dk5FudVHd6v4E=');

$provider->createReferencedBatchPayoutItem(
    ['reference_id' => 'CAPTURETXNID', 'reference_type' => 'TRANSACTION_ID'],
    'some-request-id', 'some-attribution-id'
);

$provider->showReferencedPayoutItemDetails('CDZEC5MJ8R5HY', 'some-attribution-id');
```

---

## Reference Transactions (Billing Agreements)

> **Note:** This is a [limited-release PayPal API](https://developer.paypal.com/limited-release/reference-transactions/). You must request access from PayPal before using it.

```php
// Create an agreement token (first step)
$provider->createBillingAgreementToken($data);

// Get details of an existing agreement token
$provider->getBillingAgreementTokenDetails('token-id');

// Create a billing agreement from a token
$provider->createBillingAgreement('token-id');

// Show / Update / Cancel a billing agreement
$provider->showBillingAgreementDetails('agreement-id');
$provider->updateBillingAgreement('agreement-id', $patchData);
$provider->cancelBillingAgreement('agreement-id');
```

---

## Invoices

```php
$invoiceNo = $provider->generateInvoiceNumber();

$invoices = $provider->listInvoices();
$invoices = $provider->listInvoices(2, 50);

$invoice = $provider->createInvoice($data);
$provider->deleteInvoice('INV2-Z56S-5LLA-Q52L-CPZ5');
$provider->updateInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', $data);
$invoice = $provider->showInvoiceDetails('INV2-Z56S-5LLA-Q52L-CPZ5');

$provider->cancelInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', $data);
$provider->generateQRCodeInvoice('INV2-Z56S-5LLA-Q52L-CPZ5');
$provider->generateQRCodeInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 50, 50); // custom dimensions

$provider->sendInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 'Subject', 'Note');
$provider->sendInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 'Subject', 'Note', true, true, ['cc@example.com']);

$provider->sendInvoiceReminder('INV2-Z56S-5LLA-Q52L-CPZ5', 'Subject', 'Note');
$provider->sendInvoiceReminder('INV2-Z56S-5LLA-Q52L-CPZ5', 'Subject', 'Note', true, true, ['cc@example.com']);

$provider->registerPaymentInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', '2024-05-21', 'BANK_TRANSFER', 10.00);
$provider->deleteExternalPaymentInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 'EXTR-86F38350LX4353815');

$provider->refundInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', '2024-05-26', 'BANK_TRANSFER', 5.00);
$provider->deleteRefundInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 'EXTR-2LG703375E477444T');
```

---

## Invoice Search

```php
$invoices = $provider->searchInvoices();
$invoices = $provider->searchInvoices(1, 50, false);
```

Available filters (chainable, call `searchInvoices()` at the end):

```php
$invoices = $provider
    ->addInvoiceFilterByRecipientEmail('bill-me@example.com')
    ->addInvoiceFilterByRecipientFirstName('John')
    ->addInvoiceFilterByRecipientLastName('Doe')
    ->addInvoiceFilterByRecipientBusinessName('Acme Inc.')
    ->addInvoiceFilterByInvoiceNumber('#123')
    ->addInvoiceFilterByInvoiceStatus(['PAID', 'MARKED_AS_PAID'])
    ->addInvoiceFilterByReferenceorMemo('deal-ref')
    ->addInvoiceFilterByCurrencyCode('USD')
    ->addInvoiceFilterByAmountRange(30, 50)           // uses configured currency
    ->addInvoiceFilterByAmountRange(30, 50, 'EUR')    // explicit currency
    ->addInvoiceFilterByDateRange('2024-01-01', '2024-06-30', 'invoice_date') // invoice_date|due_date|payment_date|creation_date
    ->addInvoiceFilterByArchivedStatus(false)
    ->addInvoiceFilterByFields(['items', 'payments', 'refunds'])
    ->searchInvoices();
```

---

## Invoice Templates

```php
$provider->listInvoiceTemplates();
$provider->listInvoiceTemplates(1, 50);

$provider->createInvoiceTemplate($data);
$provider->deleteInvoiceTemplate('TEMP-19V05281TU309413B');
$provider->updateInvoiceTemplate('TEMP-19V05281TU309413B', $data);
$provider->showInvoiceTemplateDetails('TEMP-19V05281TU309413B');
```

---

## Subscriptions

Full CRUD for PayPal Subscriptions (distinct from the subscription helper methods above):

```php
$provider->createSubscription($data);

$provider->updateSubscription('I-BW452GLLEP1G', [
    ['op' => 'replace', 'path' => '/billing_info/outstanding_balance', 'value' => ['currency_code' => 'USD', 'value' => '50.00']],
]);

$provider->showSubscriptionDetails('I-BW452GLLEP1G');
$provider->activateSubscription('I-BW452GLLEP1G', 'Reactivating the subscription');
$provider->cancelSubscription('I-BW452GLLEP1G', 'Not satisfied with the service');
$provider->suspendSubscription('I-BW452GLLEP1G', 'Item out of stock');
$provider->captureSubscriptionPayment('I-BW452GLLEP1G', 'Balance reached limit', 100);
$provider->reviseSubscription('I-BW452GLLEP1G', $data);
$provider->listSubscriptionTransactions('I-BW452GLLEP1G', '2024-01-01T00:00:00Z', '2024-12-31T23:59:59Z');

// Lifecycle helpers
$provider->reactivateSubscription('I-BW452GLLEP1G');                          // default reason
$provider->reactivateSubscription('I-BW452GLLEP1G', 'Customer requested');   // custom reason

$isActive = $provider->isSubscriptionActive('I-BW452GLLEP1G'); // bool
```

---

## Disputes

```php
$provider->listDisputes();
$provider->updateDispute('PP-D-27803', $patchData);
$provider->showDisputeDetails('PP-D-27803');
```

---

## Dispute Actions

```php
$provider->acceptDisputeClaim('PP-D-27803', 'Wrong item shipped');
$provider->acceptDisputeOfferResolution('PP-D-27803', 'Accepting discount offer');
$provider->acknowledgeItemReturned('PP-D-27803', 'Items received', 'ITEM_RECEIVED');

$provider->makeOfferToResolveDispute('PP-D-27803', 'Offering refund', 10.00, 'REFUND');
$provider->escalateDisputeToClaim('PP-D-27803', 'Escalating unresolved dispute');
$provider->updateDisputeStatus('PP-D-27803', $data);

// Provide evidence (jpg, png, pdf only)
$provider->provideDisputeEvidence('PP-D-27803', [
    '/path/to/invoice.pdf',
    '/path/to/screenshot.jpg',
]);
```

---

## Trackers

The `transaction-id` used here is the capture ID — get it via `getCaptureIdFromOrder()` after calling `capturePaymentOrder()` (see [Orders](#orders)).

```php
$provider->addBatchTracking($data);
$provider->addTracking($data);
$provider->listTrackingDetails($captureId);
$provider->listTrackingDetails($captureId, 'tracking-number');
$provider->updateTrackingDetails('tracking-id', $data);
$provider->showTrackingDetails('tracking-id');
```

---

## Webhooks

```php
// Create
$provider->createWebHook('https://example.com/paypal/webhook', ['PAYMENT.CAPTURE.COMPLETED']);

// List / Show / Update / Delete
$provider->listWebHooks();
$provider->showWebHookDetails('webhook-id');
$provider->updateWebHook('webhook-id', $patchData);
$provider->deleteWebHook('webhook-id');

// Events
$provider->listWebHookEvents('webhook-id');
$provider->listEventTypes();
$provider->listEvents();
$provider->showEventDetails('event-id');
$provider->resendEventNotification('event-id', ['webhook-id']);

// Verify incoming webhook signature (API roundtrip)
$provider->verifyWebHook([
    'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
    'cert_url'          => $request->header('PAYPAL-CERT-URL'),
    'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
    'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
    'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
    'webhook_id'        => 'your-webhook-id',
    'webhook_event'     => $request->all(),
]);

// Verify locally (offline — no API roundtrip, faster for high-volume webhooks)
// Pass all request headers, your webhook ID, and the RAW (unmodified) request body.
$valid = $provider->verifyWebHookLocally(
    $request->headers->all(),
    'your-webhook-id',
    $request->getContent(),  // must be the raw body bytes, not re-encoded JSON
);
```

> **Local verification** skips the PayPal verify API entirely. The signing certificate is fetched
> over HTTPS from the `PAYPAL-CERT-URL` on the first call, then cached in memory for the lifetime
> of the process — subsequent calls are pure in-memory RSA-SHA256 with no network I/O. Short-lived
> processes (serverless, etc.) will still fetch the cert on each cold start. The cert URL is
> validated against PayPal's known API domains before any request is made (SSRF guard).

### Handling webhook events

After verification, parse the raw body into a typed `WebhookEvent` and route by event type:

```php
use Srmklive\PayPal\Events\WebhookEvent;

$rawBody = $request->getContent();

if (! $provider->verifyWebHookLocally($request->headers->all(), 'your-webhook-id', $rawBody)) {
    return response()->json(['error' => 'Invalid signature'], 401);
}

$event = WebhookEvent::fromRawBody($rawBody);

if ($event->is('PAYMENT.CAPTURE.COMPLETED')) {
    // $event->resource contains the capture object
    $this->handleCapture($event->resource);
}

if ($event->is('BILLING.SUBSCRIPTION.CANCELLED')) {
    $this->handleCancellation($event->resource);
}

// Available properties:
// $event->id           — webhook notification ID
// $event->eventType    — e.g. 'PAYMENT.CAPTURE.COMPLETED'
// $event->resourceType — e.g. 'capture'
// $event->summary      — human-readable summary
// $event->createTime   — ISO 8601 timestamp
// $event->resource     — event-specific resource array
// $event->rawPayload   — full decoded payload array
```

You can also build from an already-decoded array:

```php
$event = WebhookEvent::fromArray($request->json()->all());
```

---

## Payment Method Tokens

```php
// Payment tokens (permanent)
$provider->createPaymentSourceToken($data);
$provider->setCustomerId('customer_4029352050');  // required before listPaymentSourceTokens()
$provider->listPaymentSourceTokens(1, 10, true);
$provider->showPaymentSourceTokenDetails('token-id');
$provider->deletePaymentSourceToken('token-id');

// Setup tokens (single-use, used to create a payment token)
$provider->createPaymentSetupToken($data);
$provider->showPaymentSetupTokenDetails('token-id');
$provider->deletePaymentSetupToken('token-id');
```

Using the fluent helpers to create a token:

```php
$response = $provider->setTokenSource('5C991763VB2781612', 'SETUP_TOKEN')
    ->setCustomerId('customer_4029352050')
    ->sendPaymentMethodRequest();
// or ->sendPaymentMethodRequest(true) to create a setup token instead
```

---

## Reporting

```php
use Carbon\Carbon;

// Raw filter array (full control)
$provider->listTransactions([
    'start_date' => Carbon::now()->subDays(30)->toIso8601String(),
    'end_date'   => Carbon::now()->toIso8601String(),
]);

// Convenience helpers
$provider->getTransactionDetails('5TY05013RG002845M');        // searches last 31 days
$provider->getTransactionDetails('5TY05013RG002845M', 7);    // searches last 7 days

$provider->listTransactionsForDateRange('2024-07-01', '2024-07-31');
$provider->listTransactionsByType('T0006', '2024-07-01', '2024-07-31'); // e.g. express checkout sales
$provider->listTransactionsByStatus('S', '2024-07-01', '2024-07-31');  // 'S'=success, 'V'=reversed, 'P'=pending

$provider->listBalances('2024-01-01');
$provider->listBalances('2024-01-01', 'EUR');
```

---

## Identity

```php
$provider->showProfileInfo();

$provider->createMerchantApplication(
    'AGGREGATOR',
    ['https://example.com/callback'],
    ['merchant@example.com'],
    'WDJJHEBZ4X2LY',
    'some-open-id'
);

$provider->setAccountProperties($data);
$provider->disableAccountProperties();

$provider->listUsers(1, 10);
$provider->showUserDetails('user-id');
$provider->deleteUser('user-id');

// Client token — used with PayPal Fastlane and Advanced Card Payments
$provider->generateClientToken(); // preferred alias
$provider->getClientToken();       // equivalent
```

---

## Partner Referrals

```php
$provider->createPartnerReferral($data);
$provider->showReferralData('ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=');

$provider->listSellerTrackingInfo('tracking-id');
$provider->listSellerStatus('partner-id', 'merchant-id');
$provider->listMerchantCredentials();
```

---

## Payment Experience

```php
$provider->listWebExperienceProfiles();
$provider->createWebExperienceProfile($data);
$provider->showWebExperienceProfileDetails('XP-A88A-LYLW-8Y3X-E5ER');
$provider->updateWebExperienceProfile('XP-A88A-LYLW-8Y3X-E5ER', $data);
$provider->patchWebExperienceProfile('XP-A88A-LYLW-8Y3X-E5ER', $patchData);
$provider->deleteWebExperienceProfile('XP-A88A-LYLW-8Y3X-E5ER');
```

---

## Testing

Use `MockPayPalClient` to write unit tests against your PayPal integration without hitting the sandbox API:

```php
use Srmklive\PayPal\Testing\MockPayPalClient;

$mock = new MockPayPalClient();
$mock->addResponse(['id' => '5O190127TN364715T', 'status' => 'CREATED']);

// mockProvider() returns a ready PayPal instance — credentials and access token pre-set
$provider = $mock->mockProvider();
$order = $provider->createOrder($data);

expect($order['id'])->toBe('5O190127TN364715T');
```

Queue multiple responses in order — one is consumed per API call:

```php
$mock = new MockPayPalClient();
$mock->addResponse(['id' => 'ORDER-1', 'status' => 'CREATED']);
$mock->addResponse(['id' => 'ORDER-2', 'status' => 'CREATED']);
```

Pass `false` as the body for empty-response operations (e.g. `updateOrder`, which returns 204):

```php
$mock->addResponse(false, 204);
```

Inspect what was sent to assert on headers, method, or payload:

```php
$request = $mock->lastRequest();           // Psr\Http\Message\RequestInterface
$mock->requests();                         // all captured requests, in order
$mock->requestCount();                     // int

$request->getHeaderLine('Authorization'); // 'Bearer mock-access-token'
$request->getMethod();                    // 'POST'
(string) $request->getUri();              // 'https://api-m.sandbox.paypal.com/v2/checkout/orders'
```

If you need to inject the mock into a provider you've already constructed, pass it to `setClient()` directly — `MockPayPalClient` implements `Psr\Http\Client\ClientInterface`:

```php
$provider->setAccessToken(['access_token' => 'mock-token', 'token_type' => 'Bearer']);
$provider->setClient($mock);
```

## Maintained by Blendbyte

<br>

<p align="center">
  <a href="https://www.blendbyte.com">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://www.blendbyte.com/logo_horizontal_light.png">
      <img src="https://www.blendbyte.com/logo_horizontal.png" alt="Blendbyte" width="360">
    </picture>
  </a>
</p>

<p align="center">
  <strong><a href="https://www.blendbyte.com">Blendbyte</a></strong> builds cloud infrastructure, web apps, and developer tools.<br>
  We've been shipping software to production for 20+ years.
</p>

<p align="center">
  This package runs in our own stack, which is why we keep it maintained.<br>
  Issues and PRs get read. Good ones get merged.
</p>

<br>

<p align="center">
  <a href="https://www.blendbyte.com">blendbyte.com</a> · <a href="mailto:hello@blendbyte.com">hello@blendbyte.com</a>
</p>
