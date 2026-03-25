<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class InvoicesTemplatesTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_create_invoice_template(): void
    {
        $expectedResponse = $this->mockCreateInvoiceTemplateResponse();

        $expectedParams = $this->mockCreateInvoiceTemplateParams();

        $expectedMethod = 'createInvoiceTemplate';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_list_invoice_templates(): void
    {
        $expectedResponse = $this->mockListInvoiceTemplateResponse();

        $expectedMethod = 'listInvoiceTemplates';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }

    #[Test]
    public function it_can_delete_an_invoice_template(): void
    {
        $expectedResponse = '';

        $expectedMethod = 'deleteInvoiceTemplate';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('TEMP-19V05281TU309413B'));
    }

    #[Test]
    public function it_can_update_an_invoice_template(): void
    {
        $expectedResponse = $this->mockUpdateInvoiceTemplateResponse();

        $expectedParams = $this->mockUpdateInvoiceTemplateParams();

        $expectedMethod = 'updateInvoiceTemplate';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('TEMP-19V05281TU309413B', $expectedParams));
    }

    #[Test]
    public function it_can_get_details_for_an_invoice_template(): void
    {
        $expectedResponse = $this->mockGetInvoiceTemplateResponse();

        $expectedMethod = 'showInvoiceTemplateDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('TEMP-19V05281TU309413B'));
    }
}
