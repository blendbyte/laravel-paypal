<?php

use Carbon\Carbon;

it('can list transactions', function () {
    $expectedResponse = $this->mockListTransactionsResponse();

    $expectedMethod = 'listTransactions';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    $filters = [
        'start_date' => Carbon::now()->toIso8601String(),
        'end_date' => Carbon::now()->subDays(30)->toIso8601String(),
    ];

    expect($mockClient->{$expectedMethod}($filters))->toBe($expectedResponse);
});

it('can list balances', function () {
    $expectedResponse = $this->mockListBalancesResponse();

    $expectedMethod = 'listBalances';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('2016-10-15T06:07:00-0700'))->toBe($expectedResponse);
});
