<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Carbon\Carbon;

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('can create a monthly subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansResponse()
        )
    );

    $this->client = $this->client->addPlanTrialPricing('DAY', 7)
        ->addMonthlyPlan('Demo Plan', 'Demo Plan', 100);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can create a daily subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansResponse()
        )
    );

    $this->client = $this->client->addPlanTrialPricing('DAY', 7)
        ->addDailyPlan('Demo Plan', 'Demo Plan', 1.50);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can create a weekly subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansResponse()
        )
    );

    $this->client = $this->client->addPlanTrialPricing('DAY', 7)
        ->addWeeklyPlan('Demo Plan', 'Demo Plan', 50);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can create an annual subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansResponse()
        )
    );

    $this->client = $this->client->addPlanTrialPricing('DAY', 7)
        ->addAnnualPlan('Demo Plan', 'Demo Plan', 100);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can create a subscription with custom defined interval', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansResponse()
        )
    );

    $this->client = $this->client->addPlanTrialPricing('DAY', 7)
        ->addCustomPlan('Demo Plan', 'Demo Plan', 100, 'MONTH', 3);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('throws exception when invalid interval is provided for creating a subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client = $this->client->addProductById('PROD-XYAB12ABSB7868434');

    expect(fn () => $this->client->addCustomPlan('Demo Plan', 'Demo Plan', 100, 'MONTHLY', 3))->toThrow(RuntimeException::class);
});

it('throws exception when get error for creating a billing plan', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansErrorResponse()
        )
    );

    expect(fn () => $this->client->addMonthlyPlan('Demo Plan', 'Demo Plan', 100))->toThrow(RuntimeException::class);
});

it('throws exception when get error for creating a product', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockGetCatalogProductsErrorResponse()
        )
    );

    expect(fn () => $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE'))->toThrow(RuntimeException::class);
});

it('can create a subscription without trial', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansResponse()
        )
    );

    $this->client = $this->client->addMonthlyPlan('Demo Plan', 'Demo Plan', 100);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can create a subscription by existing product and billing plan', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
        ->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('skips product and billing plan creation if already set when creating a daily subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
        ->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
        ->addDailyPlan('Demo Plan', 'Demo Plan', 1.50);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('skips product and billing plan creation if already set when creating a weekly subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
        ->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
        ->addWeeklyPlan('Demo Plan', 'Demo Plan', 100);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('skips product and billing plan creation if already set when creating a monthly subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
        ->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
        ->addMonthlyPlan('Demo Plan', 'Demo Plan', 100);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('skips product and billing plan creation if already set when creating an annual subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
        ->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
        ->addAnnualPlan('Demo Plan', 'Demo Plan', 100);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('skips product and billing plan creation if already set when creating a subscription with custom intervals', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
        ->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
        ->addCustomPlan('Demo Plan', 'Demo Plan', 100, 'MONTH', 3);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can add setup fees when creating subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();
    $setup_fee = 9.99;

    $this->client = $this->client->addSetupFee($setup_fee)
        ->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can add shipping address when creating subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addShippingAddress('John Doe', 'House no. 123', 'Street 456', 'Test Area', 'Test Area', 10001, 'US')
        ->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can add custom payment failure threshold value when creating subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();
    $threshold = 5;

    $this->client = $this->client->addPaymentFailureThreshold($threshold)
        ->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can set tax percentage when creating subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $start_date = Carbon::now()->addDay()->toDateString();
    $percentage = 10;

    $this->client = $this->client->addTaxes($percentage)
        ->addProductById('PROD-XYAB12ABSB7868434')
        ->addBillingPlanById('P-5ML4271244454362WXNWU5NQ');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can create a subscription with fixed installments', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansResponse()
        )
    );

    $this->client = $this->client->addPlanTrialPricing('DAY', 7)
        ->addMonthlyPlan('Demo Plan', 'Demo Plan', 100, 12);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});

it('can set a custom id on a subscription', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateCatalogProductsResponse()
        )
    );

    $start_date = Carbon::now()->addDay()->toDateString();

    $this->client = $this->client->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE');

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreatePlansResponse()
        )
    );

    $this->client = $this->client->addMonthlyPlan('Demo Plan', 'Demo Plan', 100);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockCreateSubscriptionResponse()
        )
    );

    $response = $this->client
        ->addCustomId('order-ref-12345')
        ->setupSubscription('John Doe', 'john@example.com', $start_date);

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('id');
    expect($response)->toHaveKey('plan_id');
});
