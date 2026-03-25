<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class ReportingTest extends TestCase
{
    use MockClientClasses;
    use MockResponsePayloads;

    #[Test]
    public function it_can_list_transactions(): void
    {
        $expectedResponse = $this->mockListTransactionsResponse();

        $expectedMethod = 'listTransactions';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $filters = [
            'start_date'    => Carbon::now()->toIso8601String(),
            'end_date'      => Carbon::now()->subDays(30)->toIso8601String(),
        ];

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($filters));
    }

    #[Test]
    public function it_can_list_balances(): void
    {
        $expectedResponse = $this->mockListBalancesResponse();

        $expectedMethod = 'listBalances';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('2016-10-15T06:07:00-0700'));
    }
}
