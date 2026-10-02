<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->client->setClient($this->mock_http_client($this->mockAccessTokenResponse()));
    $response = $this->client->getAccessToken();
    $this->access_token = $response['access_token'];
});

it('processBillingPlanPricingUpdates throws when no billing plan is set', function () {
    $client = $this->createPartialMock(\Srmklive\PayPal\Services\PayPal::class, []);

    expect(fn () => $client->processBillingPlanPricingUpdates())
        ->toThrow(RuntimeException::class, 'No billing plan set');
});

it('sends billing_cycle_sequence and pricing_scheme when updating plan pricing', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-5ML4271244454362WXNWU5NQ')
        ->addPricingScheme('DAY', 7, 0, true)
        ->addPricingScheme('MONTH', 1, 100)
        ->processBillingPlanPricingUpdates();

    $request = $mock->lastRequest();
    expect((string) $request->getUri())->toEndWith('/v1/billing/plans/P-5ML4271244454362WXNWU5NQ/update-pricing-schemes')
        ->and(json_decode((string) $request->getBody(), true))->toBe(['pricing_schemes' => [
            ['billing_cycle_sequence' => 1, 'pricing_scheme' => ['fixed_price' => ['value' => '0.00', 'currency_code' => 'USD']]],
            ['billing_cycle_sequence' => 2, 'pricing_scheme' => ['fixed_price' => ['value' => '100.00', 'currency_code' => 'USD']]],
        ]]);
});

it('uses sequence 1 for a regular cycle without trial', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-1')->addPricingScheme('MONTH', 1, 25)->processBillingPlanPricingUpdates();

    expect(json_decode((string) $mock->lastRequest()->getBody(), true)['pricing_schemes'][0]['billing_cycle_sequence'])->toBe(1);
});

it('uses an explicit billing cycle sequence', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-1')->addPricingScheme('MONTH', 1, 25, false, 3)->processBillingPlanPricingUpdates();

    expect(json_decode((string) $mock->lastRequest()->getBody(), true)['pricing_schemes'][0]['billing_cycle_sequence'])->toBe(3);
});

it('rejects an explicit billing cycle sequence outside 1-99', function () {
    $client = (new MockPayPalClient())->mockProvider();

    expect(fn () => $client->addPricingScheme('MONTH', 1, 25, false, 100))->toThrow(InvalidArgumentException::class);
});

it('resets the trial count between pricing update batches', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(false, 204);
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-1')->addPricingScheme('DAY', 7, 0, true)->processBillingPlanPricingUpdates();
    $client->addBillingPlanById('P-2')->addPricingScheme('MONTH', 1, 25)->processBillingPlanPricingUpdates();

    expect(json_decode((string) $mock->lastRequest()->getBody(), true)['pricing_schemes'][0]['billing_cycle_sequence'])->toBe(1);
});

it('can set custom limits when listing billing plans', function () {
    $this->client->setAccessToken([
        'access_token' => $this->access_token,
        'token_type' => 'Bearer',
    ]);

    $this->client = $this->client->setPageSize(30)
        ->showTotals(true);

    $this->client->setClient(
        $this->mock_http_client(
            $this->mockListPlansResponse()
        )
    );

    $response = $this->client->setCurrentPage(1)->listPlans();

    expect($response)->not->toBeEmpty();
    expect($response)->toHaveKey('plans');
});
