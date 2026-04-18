# Laravel PayPal

> [!WARNING]
> **This branch (v1.0) is deprecated and no longer maintained.**
>
> v1.0 uses the classic PayPal NVP/SOAP API (Express Checkout, Adaptive Payments), which PayPal deprecated in 2017. No bug fixes, security patches, or new features will be made to this branch.
>
> **Upgrade to [v3.1](https://github.com/blendbyte/laravel-paypal)** — the actively maintained release built on PayPal's current REST API (Orders v2, Subscriptions v2).
>
> Migration requires a full rewrite of your checkout flow:
> - Replace `SetExpressCheckout` / `DoExpressCheckoutPayment` with `createOrder()` → `capturePaymentOrder()`
> - Replace `CreateRecurringPaymentsProfile` with the Subscriptions v2 helpers (`addProduct()` → `setupSubscription()`)
> - Replace NVP/SOAP credentials with a REST API Client ID and Secret from [developer.paypal.com](https://developer.paypal.com)
>
> See the [README](https://github.com/blendbyte/laravel-paypal/blob/main/README.md) for full installation and usage documentation.

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/srmklive/paypal.svg?style=flat-square)](https://packagist.org/packages/srmklive/paypal)
[![Total Downloads](https://img.shields.io/packagist/dt/srmklive/paypal.svg?style=flat-square)](https://packagist.org/packages/srmklive/paypal)
[![StyleCI](https://styleci.io/repos/43671533/shield?style=flat)](https://styleci.io/repos/43671533)
[![Code Quality](https://scrutinizer-ci.com/g/srmklive/laravel-paypal/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/srmklive/laravel-paypal/?branch=master)

- [Introduction](#introduction)
- [Demo Application](#demo-application)
- [PayPal API Credentials](#paypal-api-credentials)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Override PayPal API Configuration](#usage-paypal-api-configuration)
  - [Set Currency](#usage-currency)
  - [Additional PayPal API Parameters](#usage-paypal-params)
  - [Express Checkout](#usage-express-checkout)
    - [SetExpressCheckout](#usage-ec-setexpresscheckout)
    - [GetExpressCheckoutDetails](#usage-ec-getexpresscheckoutdetails)
    - [DoExpressCheckoutPayment](#usage-ec-doexpresscheckoutpayment)
    - [RefundTransaction](#usage-ec-refundtransaction)
    - [CreateBillingAgreement](#usage-ec-createbillingagreement)
    - [DoReferenceTransaction](#usage-ec-doreferencetransaction)
    - [GetTransactionDetails](#usage-ec-gettransactiondetails)
    - [CreateRecurringPaymentsProfile](#usage-ec-createrecurringprofile)
    - [GetRecurringPaymentsProfileDetails](#usage-ec-getrecurringprofiledetails)
    - [UpdateRecurringPaymentsProfile](#usage-ec-updaterecurringprofile)
    - [ManageRecurringPaymentsProfileStatus](#usage-ec-managerecurringprofile)
  - [Adaptive Payments](#usage-adaptive-payments)
    - [Pay](#usage-adaptive-pay)
- [Handling PayPal IPN](#paypalipn)
- [Creating Subscriptions](#create-subscriptions)
- [Support](#support)
- [PayPal Documentation](https://github.com/srmklive/laravel-paypal/blob/master/PAYPALDOCS.md)

    
<a name="introduction"></a>
## Introduction

By using this plugin you can process or refund payments and handle IPN (Instant Payment Notification) from PayPal in your Laravel application.

**Currently only PayPal Express Checkout API Is Supported.**

<a name="demo-application"></a>
## Demo Application

Demo Application:
https://paypal-demo.srmk.info/

Github repo
https://github.com/srmklive/laravel-paypal-demo

<a name="paypal-api-credentials"></a>
## PayPal API Credentials

This package uses the classic paypal express checkout. Refer to this link on how to create API credentials:

https://developer.paypal.com/docs/classic/api/apiCredentials/#create-an-api-signature 

<a name="installation"></a>
## Installation

* Use following command to install:

```bash
composer require srmklive/paypal:~1.0
```

* Add the service provider to your `providers[]` array in `config/app.php` file like: 

```php
Srmklive\PayPal\Providers\PayPalServiceProvider::class
```

* Add the alias to your `aliases[]` array in `config/app.php` file like: 

```php
'PayPal' => Srmklive\PayPal\Facades\PayPal::class
```

* Run the following command to publish configuration:

```bash
php artisan vendor:publish --provider "Srmklive\PayPal\Providers\PayPalServiceProvider"
```

<a name="configuration"></a>
## Configuration

* After installation, you will need to add your paypal settings. Following is the code you will find in **config/paypal.php**, which you should update accordingly.

```php
return [
    'mode'    => 'sandbox', // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
    'sandbox' => [
        'username'    => env('PAYPAL_SANDBOX_API_USERNAME', ''),
        'password'    => env('PAYPAL_SANDBOX_API_PASSWORD', ''),
        'secret'      => env('PAYPAL_SANDBOX_API_SECRET', ''),
        'certificate' => env('PAYPAL_SANDBOX_API_CERTIFICATE', ''),
        'app_id'      => 'APP-80W284485P519543T', // Used for testing Adaptive Payments API in sandbox mode
    ],
    'live' => [
        'username'    => env('PAYPAL_LIVE_API_USERNAME', ''),
        'password'    => env('PAYPAL_LIVE_API_PASSWORD', ''),
        'secret'      => env('PAYPAL_LIVE_API_SECRET', ''),
        'certificate' => env('PAYPAL_LIVE_API_CERTIFICATE', ''),
        'app_id'      => '', // Used for Adaptive Payments API
    ],

    'payment_action' => 'Sale', // Can only be 'Sale', 'Authorization' or 'Order'
    'currency'       => 'USD',
    'notify_url'     => '', // Change this accordingly for your application.
    'locale'         => '', // force gateway language  i.e. it_IT, es_ES, en_US ... (for express checkout only)
    'validate_ssl'   => true, // Validate SSL when creating api client.
];
```

* Add this to `.env.example` and `.env`

```
#PayPal Setting & API Credentials - sandbox
PAYPAL_SANDBOX_API_USERNAME=
PAYPAL_SANDBOX_API_PASSWORD=
PAYPAL_SANDBOX_API_SECRET=
PAYPAL_SANDBOX_API_CERTIFICATE=

#PayPal Setting & API Credentials - live
PAYPAL_LIVE_API_USERNAME=
PAYPAL_LIVE_API_PASSWORD=
PAYPAL_LIVE_API_SECRET=
PAYPAL_LIVE_API_CERTIFICATE=
```

<a name="usage"></a>
## Usage

Following are some ways through which you can access the paypal provider:

```php
// Import the class namespaces first, before using it directly
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\AdaptivePayments;

$provider = new ExpressCheckout;      // To use express checkout.
$provider = new AdaptivePayments;     // To use adaptive payments.

// Through facade. No need to import namespaces
$provider = PayPal::setProvider('express_checkout');      // To use express checkout(used by default).
$provider = PayPal::setProvider('adaptive_payments');     // To use adaptive payments.
```

<a name="usage-paypal-api-configuration"></a>
## Override PayPal API Configuration

You can override PayPal API configuration by calling `setApiCredentials` method:

```php
$provider->setApiCredentials($config);
```

<a name="usage-currency"></a>
## Set Currency

By default the currency used is `USD`. If you wish to change it, you may call `setCurrency` method to set a different currency before calling any respective API methods:

```php
$provider->setCurrency('EUR')->setExpressCheckout($data);
```


<a name="usage-paypal-params"></a>
## Additional PayPal API Parameters

By default only a specific set of parameters are used for PayPal API calls. However, if you wish specify any other additional parameters you may call the `addOptions` method before calling any respective API methods:

```php
$options = [
    'BRANDNAME' => 'MyBrand',
    'LOGOIMG' => 'https://example.com/mylogo.png',
    'CHANNELTYPE' => 'Merchant'
];

$provider->addOptions($options)->setExpressCheckout($data);
```

**Warning:** Any parameters should be referenced accordingly to the API call you will perform. For example, if you are performing `SetExpressCheckout`, then you must provide the parameters as documented by PayPal for `SetExpressCheckout` to `addOptions` method.

<a name="usage-express-checkout"></a>
#### Express Checkout

```php
$data = [];
$data['items'] = [
    [
        'name' => 'Product 1',
        'price' => 9.99,
        'desc'  => 'Description for product 1',
        'qty' => 1
    ],
    [
        'name' => 'Product 2',
        'price' => 4.99,
        'desc'  => 'Description for product 2',
        'qty' => 2
    ]
];

$data['invoice_id'] = 1;
$data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
$data['return_url'] = url('/payment/success');
$data['cancel_url'] = url('/cart');

$total = 0;
foreach($data['items'] as $item) {
    $total += $item['price']*$item['qty'];
}

$data['total'] = $total;

//give a discount of 10% of the order amount
$data['shipping_discount'] = round((10 / 100) * $total, 2);
```

<a name="usage-ec-setexpresscheckout"></a>
* **SetExpressCheckout**

    ```php
    $response = $provider->setExpressCheckout($data);
    
    // Use the following line when creating recurring payment profiles (subscriptions)
    $response = $provider->setExpressCheckout($data, true);
    
     // This will redirect user to PayPal
    return redirect($response['paypal_link']);
    ```

<a name="usage-ec-getexpresscheckoutdetails"></a>
* **GetExpressCheckoutDetails**

    ```php
    $response = $provider->getExpressCheckoutDetails($token);
    ```
    
<a name="usage-ec-doexpresscheckoutpayment"></a>
* **DoExpressCheckoutPayment** 

    ```php
    // Note that 'token', 'PayerID' are values returned by PayPal when it redirects to success page after successful verification of user's PayPal info.
    $response = $provider->doExpressCheckoutPayment($data, $token, $PayerID);
    ```

<a name="usage-ec-refundtransaction"></a>
* **RefundTransaction**

    ```php
    $response = $provider->refundTransaction($transactionid);
    
    // To issue partial refund, you must provide the amount as well for refund:
    $response = $provider->refundTransaction($transactionid, 9.99);      
    ```

<a name="usage-ec-createbillingagreement"></a>    
* **CreateBillingAgreement**

    ```php
    // The $token is the value returned from SetExpressCheckout API call
    $response = $provider->createBillingAgreement($token);
    ```    
    
<a name="usage-ec-doreferencetransaction"></a>    
* **DoReferenceTransaction**

    ```php
    // The $token is the value returned from CreateBillingAgreement API call
    // $action Can be Order, Sale or Authorization
    // $amount to withdraw from the given BillingAgreement defaults to $. To overwrite use $provider->addOptions
    $response = $provider->doReferenceTransaction($token,$action,$amount);
    ```  
 
<a name="usage-ec-gettransactiondetails"></a>    
* **GetTransactionDetails**

    ```php
    // The $token is the value returned from doReferenceTransaction API call
    $response = $provider->getTransactionDetails($token);
    ```

<a name="usage-ec-createrecurringprofile"></a>
* **CreateRecurringPaymentsProfile**

    ```php
    // The $token is the value returned from SetExpressCheckout API call
    $startdate = Carbon::now()->toAtomString();
    $profile_desc = !empty($data['subscription_desc']) ?
                $data['subscription_desc'] : $data['invoice_description'];
    $data = [
        'PROFILESTARTDATE' => $startdate,
        'DESC' => $profile_desc,
        'BILLINGPERIOD' => 'Month', // Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
        'BILLINGFREQUENCY' => 1, // 
        'AMT' => 10, // Billing amount for each billing cycle
        'CURRENCYCODE' => 'USD', // Currency code 
        'TRIALBILLINGPERIOD' => 'Day',  // (Optional) Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
        'TRIALBILLINGFREQUENCY' => 10, // (Optional) set 12 for monthly, 52 for yearly 
        'TRIALTOTALBILLINGCYCLES' => 1, // (Optional) Change it accordingly
        'TRIALAMT' => 0, // (Optional) Change it accordingly
    ];
    $response = $provider->createRecurringPaymentsProfile($data, $token);
    ```    

<a name="usage-ec-getrecurringprofiledetails"></a>
* **GetRecurringPaymentsProfileDetails**

    ```php
    $response = $provider->getRecurringPaymentsProfileDetails($profileid);
    ```    

<a name="usage-ec-updaterecurringprofile"></a>
* **UpdateRecurringPaymentsProfile**

    ```php
    $response = $provider->updateRecurringPaymentsProfile($data, $profileid);
    ```    

<a name="usage-ec-managerecurringprofile"></a>
* **ManageRecurringPaymentsProfileStatus**

    ```php
    // Cancel recurring payment profile
    $response = $provider->cancelRecurringPaymentsProfile($profileid);
    
    // Suspend recurring payment profile
    $response = $provider->suspendRecurringPaymentsProfile($profileid);
    
    // Reactivate recurring payment profile
    $response = $provider->reactivateRecurringPaymentsProfile($profileid);    
    ```    

<a name="usage-adaptive-payments"></a>
#### Adaptive Payments

To use adaptive payments, you must set the provider to use Adaptive Payments:

```php
PayPal::setProvider('adaptive_payments');
```

<a name="usage-adaptive-pay"></a>
* **Pay**

```php

// Change the values accordingly for your application
$data = [
    'receivers'  => [
        [
            'email' => 'johndoe@example.com',
            'amount' => 10,
            'primary' => true,
        ],
        [
            'email' => 'janedoe@example.com',
            'amount' => 5,
            'primary' => false
        ]
    ],
    'payer' => 'EACHRECEIVER', // (Optional) Describes who pays PayPal fees. Allowed values are: 'SENDER', 'PRIMARYRECEIVER', 'EACHRECEIVER' (Default), 'SECONDARYONLY'
    'return_url' => url('payment/success'), 
    'cancel_url' => url('payment/cancel'),
];

$response = $provider->createPayRequest($data);

// The above API call will return the following values if successful:
// 'responseEnvelope.ack', 'payKey', 'paymentExecStatus'

```

Next, you need to redirect the user to PayPal to authorize the payment

```php
$redirect_url = $provider->getRedirectUrl('approved', $response['payKey']);

return redirect($redirect_url);
```

<a name="paypalipn"></a>
## Handling PayPal IPN
You can also handle Instant Payment Notifications from PayPal.
Suppose you have set IPN URL to **http://example.com/ipn/notify/** in PayPal. To handle IPN you should do the following:

* First add the `ipn/notify` tp your routes file:

    ```php
    Route::post('ipn/notify','PayPalController@postNotify'); // Change it accordingly in your application
    ```
          
* Open `App\Http\Middleware\VerifyCsrfToken.php` and add your IPN route to `$excluded` routes variable.

    ```php
    'ipn/notify'
    ```
    
* Write the following code in the function where you will parse IPN response:    
    
    ```php
    /**
     * Retrieve IPN Response From PayPal
     *
     * @param \Illuminate\Http\Request $request
     */
    public function postNotify(Request $request)
    {
        // Import the namespace Srmklive\PayPal\Services\ExpressCheckout first in your controller.
        $provider = new ExpressCheckout;
        
        $request->merge(['cmd' => '_notify-validate']);
        $post = $request->all();        
        
        $response = (string) $provider->verifyIPN($post);
        
        if ($response === 'VERIFIED') {                      
            // Your code goes here ...
        }                            
    }        
    ```

<a name="create-subscriptions"></a>
## Create Subscriptions

* For example, you want to create a recurring subscriptions on paypal, first pass data to `SetExpressCheckout` API call in following format:

```php
// Always update the code below accordingly to your own requirements.
$data = [];

$data['items'] = [
    [
        'name'  => "Monthly Subscription",
        'price' => 0,
        'qty'   => 1,
    ],
];

$data['subscription_desc'] = "Monthly Subscription #1";
$data['invoice_id'] = 1;
$data['invoice_description'] = "Monthly Subscription #1";
$data['return_url'] = url('/paypal/ec-checkout-success?mode=recurring');
$data['cancel_url'] = url('/');

$total = 0;
foreach ($data['items'] as $item) {
    $total += $item['price'] * $item['qty'];
}

$data['total'] = $total;

//give a discount of 10% of the order amount
$data['shipping_discount'] = round((10 / 100) * $total, 2);
```            

* Next perform the remaining steps listed in [`SetExpressCheckout`](#usage-ec-setexpresscheckout).
* Next perform the exact steps listed in [`GetExpressCheckoutDetails`](#usage-ec-getexpresscheckoutdetails).
* Finally do the following for [`CreateRecurringPaymentsProfile`](#usage-ec-createrecurringprofile)

```php
$amount = 9.99;
$description = "Monthly Subscription #1";
$response = $provider->createMonthlySubscription($token, $amount, $description);

// To create recurring yearly subscription on PayPal
$response = $provider->createYearlySubscription($token, $amount, $description);
```
            
<a name="support"></a>
## Support

This plugin only supports Laravel 5.1 or greater.
* In case of any issues, kindly create one on the [Issues](https://github.com/srmklive/laravel-paypal/issues) section.
* If you would like to contribute:
  * Fork this repository.
  * Implement your features.
  * Generate pull request.
 
