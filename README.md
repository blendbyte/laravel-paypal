# Laravel PayPal

> [!WARNING]
> **This branch (v2.0) is deprecated and no longer maintained.**
>
> v2.0 uses the PayPal REST v1 Payments API (`/v1/payments/payment`), which PayPal is sunsetting in **January 2027**. No bug fixes, security patches, or new features will be made to this branch.
>
> **Upgrade to [v3.1](https://github.com/blendbyte/laravel-paypal)** — the actively maintained release that uses Orders v2 and Subscriptions v2 throughout.
>
> Key changes when migrating:
> - Replace the create-payment → redirect → execute-payment flow with `createOrder()` → `capturePaymentOrder()`
> - Replace `/v1/billing-agreements/` with the Subscriptions v2 helpers (`addProduct()` → `setupSubscription()`)
> - Requires PHP 8.2+ and Laravel 12+
>
> See the [v3.1 README](https://github.com/blendbyte/laravel-paypal/blob/v3.1/README.md) for full installation, migration guidance, and usage documentation.

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/srmklive/paypal.svg?style=flat-square)](https://packagist.org/packages/srmklive/paypal)
[![Total Downloads](https://img.shields.io/packagist/dt/srmklive/paypal.svg?style=flat-square)](https://packagist.org/packages/srmklive/paypal)
[![StyleCI](https://github.styleci.io/repos/43671533/shield?branch=v2.0)](https://github.styleci.io/repos/43671533?branch=v2.0)
![Tests](https://github.com/srmklive/laravel-paypal/workflows/TestsV3/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/srmklive/laravel-paypal/badge.svg?branch=v3.0)](https://coveralls.io/github/srmklive/laravel-paypal?branch=v3.0)
[![Code Quality](https://scrutinizer-ci.com/g/srmklive/laravel-paypal/badges/quality-score.png?b=v3.0)](https://scrutinizer-ci.com/g/srmklive/laravel-paypal/?branch=v3.0)

- [Documentation](#introduction)
- [Usage](#usage)
- [Support](#support)

    
<a name="introduction"></a>
## Documentation

The documentation for the package can be viewed by clicking the following link:

[https://laravel-paypal.readthedocs.io/en/latest/](https://laravel-paypal.readthedocs.io/en/latest/)

The old documentation can be found at the following link:

[https://srmklive.github.io/laravel-paypal/docs.html](https://srmklive.github.io/laravel-paypal/docs.html)

<a name="usage"></a>
## Usage

Following are some ways through which you can access the paypal provider:

```php
// Import the class namespaces first, before using it directly
use Srmklive\PayPal\Services\PayPal as PayPalClient;

$provider = new PayPalClient;

// Through facade. No need to import namespaces
$provider = \PayPal::setProvider();
```

<a name="usage-paypal-api-configuration"></a>
## Configuration File

The configuration file **paypal.php** is located in the **config** folder. Following are its contents when published:

```php
return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'), // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
    'sandbox' => [
        'client_id'         => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
        'app_id'            => 'APP-80W284485P519543T',
    ],
    'live' => [
        'client_id'         => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
        'app_id'            => env('PAYPAL_LIVE_APP_ID', ''),
    ],

    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'), // Can only be 'Sale', 'Authorization' or 'Order'
    'currency'       => env('PAYPAL_CURRENCY', 'USD'),
    'notify_url'     => env('PAYPAL_NOTIFY_URL', ''), // Change this accordingly for your application.
    'locale'         => env('PAYPAL_LOCALE', 'en_US'), // force gateway language  i.e. it_IT, es_ES, en_US ... (for express checkout only)
    'validate_ssl'   => env('PAYPAL_VALIDATE_SSL', true), // Validate SSL when creating api client.
];
```

## Override PayPal API Configuration

You can override PayPal API configuration by calling `setApiCredentials` method:

```php
$config = [
    'mode'    => 'live',
    'live' => [
        'client_id'         => 'PAYPAL_LIVE_CLIENT_ID',
        'client_secret'     => 'PAYPAL_LIVE_CLIENT_SECRET',
        'app_id'            => 'PAYPAL_LIVE_APP_ID',
    ],

    'payment_action' => 'Sale',
    'currency'       => 'USD',
    'notify_url'     => 'https://your-site.com/paypal/notify',
    'locale'         => 'en_US',
    'validate_ssl'   => true,
];
$provider->setApiCredentials($config);
```


<a name="usage-paypal-get-access-token"></a>
## Get Access Token

After setting the PayPal API configuration by calling `setApiCredentials` method. You need to get access token before performing any API calls

```php
$provider->getAccessToken();
```


<a name="usage-currency"></a>
## Set Currency

By default, the currency used is `USD`. If you wish to change it, you may call `setCurrency` method to set a different currency before calling any respective API methods:

```php
$provider->setCurrency('EUR');
```

## Create Recurring Daily Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addSubscriptionTrialPricing('DAY', 7)
            ->addDailyPlan('Demo Plan', 'Demo Plan', 1.50)
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10') ;
```

## Create Recurring Weekly Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addSubscriptionTrialPricing('DAY', 7)
            ->addWeeklyPlan('Demo Plan', 'Demo Plan', 30)
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10') ;
```

## Create Recurring Monthly Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addSubscriptionTrialPricing('DAY', 7)
            ->addMonthlyPlan('Demo Plan', 'Demo Plan', 100)
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10') ;
```

## Create Recurring Annual Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addSubscriptionTrialPricing('DAY', 7)
            ->addAnnualPlan('Demo Plan', 'Demo Plan', 600)
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10') ;
```

## Create Subscription by Existing Product & Billing Plan

<a name="usage-helpers"></a>
## Helper Methods

> Please note that in the examples below, the call to `addPlanTrialPricing` is optional and it can be omitted when you are creating subscriptions without trial period.

> `setReturnAndCancelUrl()` is optional. If you set urls you have to use real domains. e.g. localhost, project.test does not work.

### Create Recurring Daily Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addPlanTrialPricing('DAY', 7)
            ->addDailyPlan('Demo Plan', 'Demo Plan', 1.50)
            ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10');
```

### Create Recurring Weekly Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addPlanTrialPricing('DAY', 7)
            ->addWeeklyPlan('Demo Plan', 'Demo Plan', 30)
            ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10');
```

### Create Recurring Monthly Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addPlanTrialPricing('DAY', 7)
            ->addMonthlyPlan('Demo Plan', 'Demo Plan', 100)
            ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10');
```

### Create Recurring Annual Subscription

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addPlanTrialPricing('DAY', 7)
            ->addAnnualPlan('Demo Plan', 'Demo Plan', 600)
            ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10');
```

### Create Recurring Subscription with Custom Intervals

```php
$response = $provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
            ->addCustomPlan('Demo Plan', 'Demo Plan', 150, 'MONTH', 3)
            ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
            ->setupSubscription('John Doe', 'john@example.com', '2021-12-10');
```

### Create Subscription by Existing Product & Billing Plan

```php
$response = $this->client->addProductById('PROD-XYAB12ABSB7868434')
    ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
    ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
    ->setupSubscription('John Doe', 'john@example.com', $start_date);
```

<a name="support"></a>
## Support

This version supports Laravel 6 or greater.
* In case of any issues, kindly create one on the [Issues](https://github.com/srmklive/laravel-paypal/issues) section.
* If you would like to contribute:
  * Fork this repository.
  * Implement your features.
  * Generate pull request.
 
