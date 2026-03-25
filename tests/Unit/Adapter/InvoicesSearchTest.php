<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class InvoicesSearchTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_search_invoices(): void
    {
        $expectedResponse = $this->mockSearchInvoicesResponse();

        $expectedMethod = 'searchInvoices';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}(1, 1, true));
    }

    #[Test]
    public function it_can_search_invoices_with_custom_filters(): void
    {
        $expectedResponse = $this->mockSearchInvoicesResponse();

        $expectedMethod = 'searchInvoices';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->addInvoiceFilterByRecipientEmail('bill-me@example.com')
            ->addInvoiceFilterByCurrencyCode('USD')
            ->addInvoiceFilterByAmountRange(30, 50)
            ->{$expectedMethod}(1, 1, true));
    }
}
