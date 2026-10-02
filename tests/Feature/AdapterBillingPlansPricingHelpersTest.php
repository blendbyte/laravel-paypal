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

function planWithCycles(array $cycles): array
{
    return ['id' => 'P-1', 'billing_cycles' => array_map(
        fn ($cycle) => ['tenure_type' => $cycle[0], 'sequence' => $cycle[1]],
        $cycles
    )];
}

function pricingUpdate(MockPayPalClient $mock): array
{
    return json_decode((string) $mock->lastRequest()->getBody(), true)['pricing_schemes'];
}

it('maps prices to the plan billing cycles when updating plan pricing', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(planWithCycles([['TRIAL', 1], ['REGULAR', 2]]));
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-1')
        ->addPricingScheme('DAY', 7, 0, true)
        ->addPricingScheme('MONTH', 1, 100)
        ->processBillingPlanPricingUpdates();

    [$lookup, $update] = $mock->requests();
    expect($lookup->getMethod())->toBe('GET')
        ->and((string) $lookup->getUri())->toEndWith('/v1/billing/plans/P-1')
        ->and((string) $update->getUri())->toEndWith('/v1/billing/plans/P-1/update-pricing-schemes')
        ->and(pricingUpdate($mock))->toBe([
            ['billing_cycle_sequence' => 1, 'pricing_scheme' => ['fixed_price' => ['value' => '0.00', 'currency_code' => 'USD']]],
            ['billing_cycle_sequence' => 2, 'pricing_scheme' => ['fixed_price' => ['value' => '100.00', 'currency_code' => 'USD']]],
        ]);
});

it('updates only the regular price of a plan with a trial', function () {
    // Regression: deriving the sequence from the call order sent 1 and repriced the trial.
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(planWithCycles([['TRIAL', 1], ['REGULAR', 2]]));
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-1')->addPricingScheme('MONTH', 1, 25)->processBillingPlanPricingUpdates();

    expect(pricingUpdate($mock)[0]['billing_cycle_sequence'])->toBe(2);
});

it('maps prices regardless of the order they were added in', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(planWithCycles([['REGULAR', 3], ['TRIAL', 2], ['TRIAL', 1]]));
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-1')
        ->addPricingScheme('MONTH', 1, 30)
        ->addPricingScheme('DAY', 7, 0, true)
        ->addPricingScheme('MONTH', 1, 10, true)
        ->processBillingPlanPricingUpdates();

    expect(array_column(pricingUpdate($mock), 'billing_cycle_sequence'))->toBe([3, 1, 2]);
});

it('uses an explicit billing cycle sequence without looking up the plan', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-1')->addPricingScheme('MONTH', 1, 25, false, 3)->processBillingPlanPricingUpdates();

    expect($mock->requests())->toHaveCount(1)
        ->and(pricingUpdate($mock)[0]['billing_cycle_sequence'])->toBe(3);
});

it('returns the plan lookup error without updating pricing', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(['name' => 'RESOURCE_NOT_FOUND'], 404);

    $response = $client->addBillingPlanById('P-404')->addPricingScheme('MONTH', 1, 25)->processBillingPlanPricingUpdates();

    expect($response)->toBe(['error' => ['name' => 'RESOURCE_NOT_FOUND']])
        ->and($mock->requests())->toHaveCount(1);
});

it('throws when a price has no matching billing cycle in the plan', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(planWithCycles([['REGULAR', 1]]));

    expect(fn () => $client->addBillingPlanById('P-1')->addPricingScheme('DAY', 7, 0, true)->processBillingPlanPricingUpdates())
        ->toThrow(RuntimeException::class, 'no matching trial billing cycle');
});

it('rejects an explicit billing cycle sequence outside 1-99', function () {
    $client = (new MockPayPalClient())->mockProvider();

    expect(fn () => $client->addPricingScheme('MONTH', 1, 25, false, 100))->toThrow(InvalidArgumentException::class);
});

it('does not carry pricing schemes over to the next batch', function () {
    $mock = new MockPayPalClient();
    $client = $mock->mockProvider();
    $mock->addResponse(false, 204);
    $mock->addResponse(false, 204);

    $client->addBillingPlanById('P-1')->addPricingScheme('MONTH', 1, 25, false, 1)->processBillingPlanPricingUpdates();
    $client->addBillingPlanById('P-2')->addPricingScheme('MONTH', 1, 30, false, 2)->processBillingPlanPricingUpdates();

    expect(pricingUpdate($mock))->toHaveCount(1);
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
