# Laravel PayPal

[![Latest Version on Packagist](https://img.shields.io/packagist/v/blendbyte/paypal.svg?style=flat-square)](https://packagist.org/packages/blendbyte/paypal)
[![Tests](https://github.com/blendbyte/laravel-paypal/actions/workflows/tests.yml/badge.svg)](https://github.com/blendbyte/laravel-paypal/actions/workflows/tests.yml)
[![Static Analysis](https://github.com/blendbyte/laravel-paypal/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/blendbyte/laravel-paypal/actions/workflows/static-analysis.yml)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A PayPal REST API package for Laravel 12+. This is a maintained fork of [srmklive/laravel-paypal](https://github.com/srmklive/laravel-paypal), modernized for current PHP and Laravel versions. It is a **drop-in replacement** — the API is identical, only the package name and root namespace change.

**Supports:** PHP 8.2–8.5 · Laravel 12 / 13

---

- [Migrating from srmklive/laravel-paypal](#migrating-from-srmklivelaravel-paypal)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Subscription Helpers](#subscription-helpers)
- [Billing Plans](#billing-plans)
- [Catalog Products](#catalog-products)
- [Orders](#orders)
- [Payments](#payments)
- [Payouts](#payouts)
- [Referenced Payouts](#referenced-payouts)
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

## Migrating from srmklive/laravel-paypal

If you're on `srmklive/laravel-paypal` v3 and need Laravel 12+ or PHP 8.2+ support, migration is straightforward:

**1. Swap the package:**

```bash
composer remove srmklive/paypal
composer require blendbyte/paypal
```

**2. Replace the namespace** in any file that references the old package:

```bash
# macOS/Linux
grep -rl 'Srmklive\\PayPal' app/ | xargs sed -i '' 's/Srmklive\\PayPal/Blendbyte\\PayPal/g'
```

Or find/replace `Srmklive\PayPal` → `Blendbyte\PayPal` in your IDE.

**3. Update the facade alias** in `config/app.php` if you have it registered manually:

```php
// Before
'PayPal' => Srmklive\PayPal\Facades\PayPal::class,

// After
'PayPal' => Blendbyte\PayPal\Facades\PayPal::class,
```

**4. Re-publish the config** (only if you want to reset it — your existing `config/paypal.php` will continue to work as-is):

```bash
php artisan vendor:publish --provider "Blendbyte\PayPal\Providers\PayPalServiceProvider" --force
```

That's it. No API changes, no method renames, no config structure changes.

---

## Installation

```bash
composer require blendbyte/paypal
```

Publish the config file:

```bash
php artisan vendor:publish --provider "Blendbyte\PayPal\Providers\PayPalServiceProvider"
```

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
    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'), // 'Sale', 'Authorization', or 'Order'
    'currency'       => env('PAYPAL_CURRENCY', 'USD'),
    'notify_url'     => env('PAYPAL_NOTIFY_URL', ''),
    'locale'         => env('PAYPAL_LOCALE', 'en_US'),
    'validate_ssl'   => env('PAYPAL_VALIDATE_SSL', true),
];
```

---

## Usage

### Initialization

```php
use Blendbyte\PayPal\Services\PayPal as PayPalClient;

$provider = new PayPalClient;

// Or via facade
$provider = \PayPal::setProvider();
```

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

// Update, show, authorize, capture
$provider->updateOrder('5O190127TN364715T', $patchData);
$order = $provider->showOrderDetails('5O190127TN364715T');
$provider->authorizePaymentOrder('5O190127TN364715T');
$provider->capturePaymentOrder('5O190127TN364715T');
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

```php
$provider->addBatchTracking($data);
$provider->addTracking($data);
$provider->listTrackingDetails('transaction-id');
$provider->listTrackingDetails('transaction-id', 'tracking-number');
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

// Verify incoming webhook signature
$provider->verifyWebHook([
    'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
    'cert_url'          => $request->header('PAYPAL-CERT-URL'),
    'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
    'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
    'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
    'webhook_id'        => 'your-webhook-id',
    'webhook_event'     => $request->all(),
]);
```

---

## Payment Method Tokens

```php
$provider->createPaymentSourceToken($data);
$provider->listPaymentSourceTokens(1, 10, true);
$provider->showPaymentSourceTokenDetails('token-id');
$provider->deletePaymentSourceToken('token-id');

$provider->createPaymentSetupToken($data);
$provider->showPaymentSetupTokenDetails('token-id');
```

---

## Reporting

```php
use Carbon\Carbon;

$provider->listTransactions([
    'start_date' => Carbon::now()->toIso8601String(),
    'end_date'   => Carbon::now()->addDays(30)->toIso8601String(),
]);

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
$provider->getClientToken();
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

