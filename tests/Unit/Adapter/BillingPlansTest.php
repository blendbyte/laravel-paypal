<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create a billing plan', function () {
    $expectedResponse = $this->mockCreatePlansResponse();

    $expectedParams = $this->createPlanParams();

    $expectedMethod = 'createPlan';
    $additionalMethod = 'setRequestHeader';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();
    $mockClient->{$additionalMethod}('PayPal-Request-Id', 'some-request-id');

    expect($mockClient->{$expectedMethod}($expectedParams, 'some-request-id'))->toBe($expectedResponse);
});

it('can list billing plans', function () {
    $expectedResponse = $this->mockListPlansResponse();

    $expectedMethod = 'listPlans';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}(1, 2, true))->toBe($expectedResponse);
});

it('can update a billing plan', function () {
    $expectedResponse = '';

    $expectedParams = $this->updatePlanParams();

    $expectedMethod = 'updatePlan';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('P-7GL4271244454362WXNWU5NQ', $expectedParams))->toBe($expectedResponse);
});

it('can show details for a billing plan', function () {
    $expectedResponse = $this->mockGetPlansResponse();

    $expectedMethod = 'showPlanDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('P-7GL4271244454362WXNWU5NQ'))->toBe($expectedResponse);
});

it('can activate a billing plan', function () {
    $expectedResponse = '';

    $expectedMethod = 'activatePlan';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('P-7GL4271244454362WXNWU5NQ'))->toBe($expectedResponse);
});

it('can deactivate a billing plan', function () {
    $expectedResponse = '';

    $expectedMethod = 'deactivatePlan';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('P-7GL4271244454362WXNWU5NQ'))->toBe($expectedResponse);
});

it('can update pricing for a billing plan', function () {
    $expectedResponse = '';

    $expectedParams = $this->updatePlanPricingParams();

    $expectedMethod = 'updatePlanPricing';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('P-2UF78835G6983425GLSM44MA', $expectedParams))->toBe($expectedResponse);
});
