<?php

namespace Blendbyte\PayPal\Tests\Feature;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Services\PayPal as PayPalClient;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class AdapterFeatureTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    /** @var string */
    protected static string $access_token = '';

    /** @var string */
    protected static string $product_id = '';

    /** @var PayPalClient */
    protected PayPalClient $client;

    protected function setUp(): void
    {
        try {
            $this->client = new PayPalClient($this->getApiCredentials());
        } catch (\Exception $e) {
        }

        parent::setUp();
    }

    #[Test]
    public function it_returns_error_if_invalid_credentials_are_used_to_get_access_token(): void
    {
        $this->client = new PayPalClient($this->getMockCredentials());
        $response = $this->client->getAccessToken();

        $this->assertIsArray($response['error']);
        $this->assertArrayHasKey('error', $response);
    }

    #[Test]
    public function it_can_get_access_token(): void
    {
        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAccessTokenResponse()
            )
        );
        $response = $this->client->getAccessToken();

        self::$access_token = $response['access_token'];

        $this->assertArrayHasKey('access_token', $response);
        $this->assertNotEmpty($response['access_token']);
    }

    #[Test]
    public function it_can_create_a_billing_agreement_token(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateBillingAgreementTokenResponse()
            )
        );

        $expectedParams = $this->createBillingAgreementTokenParams();

        try {
            $response = $this->client->setRequestHeader('PayPal-Request-Id', 'some-request-id')->createBillingAgreementToken($expectedParams);
        } catch (\Throwable $e) {
        }

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('token_id', $response);
    }

    #[Test]
    public function it_can_show_billing_agreement_token_details(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetBillingAgreementTokenResponse()
            )
        );

        try {
            $response = $this->client->getBillingAgreementTokenDetails('BA-8A802366G0648845Y');
        } catch (\Throwable $e) {
        }

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('token_id', $response);
    }

    #[Test]
    public function it_can_create_a_billing_agreement(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateBillingAgreementResponse()
            )
        );

        try {
            $response = $this->client->setRequestHeader('PayPal-Request-Id', 'some-request-id')->createBillingAgreement('BA-8A802366G0648845Y');
        } catch (\Throwable $e) {
        }

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_update_a_billing_agreement(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updateBillingAgreementParams();

        try {
            $response = $this->client->updateBillingAgreement('BA-8A802366G0648845Y', $expectedParams);
        } catch (\Throwable $e) {
        }

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_show_billing_agreement_details(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockShowBillingAgreementResponse()
            )
        );

        try {
            $response = $this->client->showBillingAgreementDetails('BA-8A802366G0648845Y');
        } catch (\Throwable $e) {
        }

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_cancel_a_billing_agreement(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        try {
            $response = $this->client->cancelBillingAgreement('BA-8A802366G0648845Y');
        } catch (\Throwable $e) {
        }

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_create_a_billing_plan(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePlansResponse()
            )
        );

        $expectedParams = $this->createPlanParams();

        try {
            $response = $this->client->setRequestHeader('PayPal-Request-Id', 'some-request-id')->createPlan($expectedParams);
        } catch (\Throwable $e) {
        }

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_list_billing_plans(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListPlansResponse()
            )
        );

        $response = $this->client->listPlans();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('plans', $response);
    }

    #[Test]
    public function it_can_update_a_billing_plan(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updatePlanParams();

        $response = $this->client->updatePlan('P-7GL4271244454362WXNWU5NQ', $expectedParams);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_show_details_for_a_billing_plan(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetPlansResponse()
            )
        );

        $response = $this->client->showPlanDetails('P-5ML4271244454362WXNWU5NQ');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_activate_a_billing_plan(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->activatePlan('P-7GL4271244454362WXNWU5NQ');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_deactivate_a_billing_plan(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deactivatePlan('P-7GL4271244454362WXNWU5NQ');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_update_pricing_for_a_billing_plan(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updatePlanPricingParams();

        $response = $this->client->updatePlanPricing('P-2UF78835G6983425GLSM44MA', $expectedParams);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_list_products(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListCatalogProductsResponse()
            )
        );

        $response = $this->client->listProducts();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('products', $response);
    }

    #[Test]
    public function it_can_create_a_product(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateCatalogProductsResponse()
            )
        );

        $expectedParams = $this->createProductParams();

        $response = $this->client->setRequestHeader('PayPal-Request-Id', 'product-request-'.time())->createProduct($expectedParams);

        self::$product_id = $response['id'];

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_update_a_product(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updateProductParams();

        $response = $this->client->updateProduct(self::$product_id, $expectedParams);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_get_details_for_a_product(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetCatalogProductsResponse()
            )
        );

        $response = $this->client->showProductDetails(self::$product_id);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_acknowledge_item_is_returned_for_raised_dispute(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $response = $this->client->acknowledgeItemReturned(
            'PP-D-4012',
            'I have received the item back.',
            'ITEM_RECEIVED'
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_list_disputes(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListDisputesResponse()
            )
        );

        $response = $this->client->listDisputes();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('items', $response);
    }

    #[Test]
    public function it_can_partially_update_a_dispute(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updateDisputeParams();

        $response = $this->client->updateDispute('PP-D-27803', $expectedParams);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_get_details_for_a_dispute(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetDisputesResponse()
            )
        );

        $response = $this->client->showDisputeDetails('PP-D-4012');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('dispute_id', $response);
    }

    #[Test]
    public function it_can_provide_evidence_for_a_dispute_claim(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $mockFiles = [
            __DIR__.'/../Mocks/samples/sample.jpg',
            __DIR__.'/../Mocks/samples/sample.png',
            __DIR__.'/../Mocks/samples/sample.pdf',
        ];

        $response = $this->client->provideDisputeEvidence(
            'PP-D-27803',
            $mockFiles
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);

        $this->markTestIncomplete('Skipping the test');
    }

    #[Test]
    public function it_throws_exception_if_invalid_file_as_evidence_is_provided_for_a_dispute_claim(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $mockFiles = [
            __DIR__.'/../Mocks/samples/sample.txt',
            __DIR__.'/../Mocks/samples/sample.pdf',
        ];

        $this->expectException(\Exception::class);

        $this->markTestIncomplete('Skipping the test');

        $response = $this->client->provideDisputeEvidence(
            'PP-D-27803',
            $mockFiles
        );
    }

    #[Test]
    public function it_throws_exception_if_file_size_as_evidence_exceeds_per_file_limit_for_a_dispute_claim(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $file = __DIR__.'/../Mocks/samples/sample2.pdf';

        $mockFiles = [$file];

        $this->expectException(\Exception::class);

        $this->markTestIncomplete('Skipping the test');

        $this->client->provideDisputeEvidence(
            'PP-D-27803',
            $mockFiles
        );
    }

    #[Test]
    public function it_throws_exception_if_file_size_as_evidence_exceeds_overall_limit_for_a_dispute_claim(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $file = __DIR__.'/../Mocks/samples/sample2.pdf';

        $mockFiles = [$file, $file, $file, $file, $file];

        $this->expectException(\Exception::class);

        $this->markTestIncomplete('Skipping the test');

        $this->client->provideDisputeEvidence(
            'PP-D-27803',
            $mockFiles
        );
    }

    #[Test]
    public function it_can_offer_to_resolve_dispute_claim(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $response = $this->client->makeOfferToResolveDispute(
            'PP-D-27803',
            'Offer refund with replacement item.',
            5.99,
            'REFUND_WITH_REPLACEMENT'
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_escalate_dispute_claim(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $response = $this->client->escalateDisputeToClaim(
            'PP-D-27803',
            'Escalating to PayPal claim for resolution.'
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_accept_dispute_claim(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $response = $this->client->acceptDisputeClaim(
            'PP-D-27803',
            'Full refund to the customer.'
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_accept_dispute_offer_resolution(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $response = $this->client->acceptDisputeOfferResolution(
            'PP-D-4012',
            'I am ok with the refund offered.'
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_update_dispute_status(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $response = $this->client->updateDisputeStatus(
            'PP-D-4012',
            true
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_settle_dispute(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $response = $this->client->settleDispute(
            'PP-D-4012',
            true
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_decline_dispute_offer_resolution(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockAcceptDisputesClaimResponse()
            )
        );

        $response = $this->client->declineDisputeOfferResolution(
            'PP-D-4012',
            'I am not ok with the refund offered.'
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_generate_unique_invoice_number(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGenerateInvoiceNumberResponse()
            )
        );

        $response = $this->client->generateInvoiceNumber();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('invoice_number', $response);
    }

    #[Test]
    public function it_can_create_a_draft_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateInvoicesResponse()
            )
        );

        $expectedParams = $this->createInvoiceParams();

        $response = $this->client->createInvoice($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_list_invoices(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListInvoicesResponse()
            )
        );

        $response = $this->client->listInvoices();

        $this->assertArrayHasKey('total_pages', $response);
        $this->assertArrayHasKey('total_items', $response);
    }

    #[Test]
    public function it_can_delete_an_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteInvoice('INV2-Z56S-5LLA-Q52L-CPZ5');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_update_an_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateInvoicesResponse()
            )
        );

        $expectedParams = $this->updateInvoiceParams();

        $response = $this->client->updateInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', $expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_show_details_for_an_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetInvoicesResponse()
            )
        );

        $response = $this->client->showInvoiceDetails('INV2-Z56S-5LLA-Q52L-CPZ5');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_cancel_an_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->cancelInvoiceParams();

        $response = $this->client->cancelInvoice(
            'INV2-Z56S-5LLA-Q52L-CPZ5',
            'Payment due for the invoice #ABC-123',
            'Please pay before the due date to avoid incurring late payment charges which will be adjusted in the next bill generated.',
            true,
            true,
            [
                'customer-a@example.com',
                'customer@example.com',
            ]
        );

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_generate_qr_code_for_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGenerateInvoiceQRCodeResponse()
            )
        );

        $response = $this->client->generateQRCodeInvoice('INV2-Z56S-5LLA-Q52L-CPZ5');

        $this->assertNotEmpty($response);
    }

    #[Test]
    public function it_can_register_payment_for_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockInvoiceRegisterPaymentResponse()
            )
        );

        $response = $this->client->registerPaymentInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', '2018-05-01', 'BANK_TRANSFER', 10.00);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('payment_id', $response);
    }

    #[Test]
    public function it_can_delete_payment_for_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteExternalPaymentInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 'EXTR-86F38350LX4353815');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_refund_payment_for_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockInvoiceRefundPaymentResponse()
            )
        );

        $response = $this->client->refundInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', '2018-05-01', 'BANK_TRANSFER', 5.00);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('refund_id', $response);
    }

    #[Test]
    public function it_can_delete_refund_for_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteRefundInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 'EXTR-2LG703375E477444T');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_send_an_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->sendInvoice(
            'INV2-Z56S-5LLA-Q52L-CPZ5',
            'Payment due for the invoice #ABC-123',
            'Please pay before the due date to avoid incurring late payment charges which will be adjusted in the next bill generated.',
            true,
            true,
            [
                'customer-a@example.com',
                'customer@example.com',
            ]
        );

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_send_reminder_for_an_invoice(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->sendInvoiceReminder(
            'INV2-Z56S-5LLA-Q52L-CPZ5',
            'Reminder: Payment due for the invoice #ABC-123',
            'Please pay before the due date to avoid incurring late payment charges which will be adjusted in the next bill generated.',
            true,
            true,
            [
                'customer-a@example.com',
                'customer@example.com',
            ]
        );

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_create_invoice_template(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateInvoiceTemplateResponse()
            )
        );

        $expectedParams = $this->mockCreateInvoiceTemplateParams();

        $response = $this->client->createInvoiceTemplate($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_list_invoice_templates(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListInvoiceTemplateResponse()
            )
        );

        $response = $this->client->listInvoiceTemplates();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('templates', $response);
    }

    #[Test]
    public function it_can_delete_an_invoice_template(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteInvoiceTemplate('TEMP-19V05281TU309413B');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_update_an_invoice_template(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateInvoiceTemplateResponse()
            )
        );

        $expectedParams = $this->mockUpdateInvoiceTemplateParams();

        $response = $this->client->updateInvoiceTemplate('TEMP-19V05281TU309413B', $expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_get_details_for_an_invoice_template(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetInvoiceTemplateResponse()
            )
        );

        $response = $this->client->showInvoiceTemplateDetails('TEMP-19V05281TU309413B');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_search_invoices(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $response = $this->client->searchInvoices();

        $this->assertArrayHasKey('total_pages', $response);
        $this->assertArrayHasKey('total_items', $response);
    }

    #[Test]
    public function it_can_search_invoices_with_custom_filters(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $response = $this->client
            ->addInvoiceFilterByRecipientEmail('bill-me@example.com')
            ->addInvoiceFilterByRecipientFirstName('John')
            ->addInvoiceFilterByRecipientLastName('Doe')
            ->addInvoiceFilterByRecipientBusinessName('Acme Inc.')
            ->addInvoiceFilterByInvoiceNumber('#123')
            ->addInvoiceFilterByInvoiceStatus(['PAID', 'MARKED_AS_PAID'])
            ->addInvoiceFilterByReferenceorMemo('deal-ref')
            ->addInvoiceFilterByCurrencyCode('USD')
            ->addInvoiceFilterByAmountRange(30, 50)
            ->addInvoiceFilterByDateRange('2018-06-01', '2018-06-21', 'invoice_date')
            ->addInvoiceFilterByArchivedStatus(false)
            ->addInvoiceFilterByFields(['items', 'payments', 'refunds'])
            ->searchInvoices();

        $this->assertArrayHasKey('total_pages', $response);
        $this->assertArrayHasKey('total_items', $response);
        $this->assertArrayHasKey('items', $response);
    }

    #[Test]
    public function it_throws_exception_on_search_invoices_with_invalid_status(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $this->expectException(\Exception::class);

        $response = $this->client
            ->addInvoiceFilterByInvoiceStatus(['DECLINED'])
            ->searchInvoices();
    }

    #[Test]
    public function it_throws_exception_on_search_invoices_with_invalid_amount_ranges(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $filters = $this->invoiceSearchParams();

        $this->expectException(\Exception::class);

        $response = $this->client
            ->addInvoiceFilterByAmountRange(50, 30)
            ->searchInvoices();
    }

    #[Test]
    public function it_throws_exception_on_search_invoices_with_invalid_date_ranges(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $filters = $this->invoiceSearchParams();

        $this->expectException(\Exception::class);

        $response = $this->client
            ->addInvoiceFilterByDateRange('2018-07-01', '2018-06-21', 'invoice_date')
            ->searchInvoices();
    }

    #[Test]
    public function it_throws_exception_on_search_invoices_with_invalid_date_range_type(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $filters = $this->invoiceSearchParams();

        $this->expectException(\Exception::class);

        $response = $this->client
            ->addInvoiceFilterByDateRange('2018-06-01', '2018-06-21', 'declined_date')
            ->searchInvoices();
    }

    #[Test]
    public function it_can_get_user_profile_details(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockShowProfileInfoResponse()
            )
        );

        $response = $this->client->showProfileInfo();

        $this->assertArrayHasKey('address', $response);
    }

    #[Test]
    public function it_can_get_list_users(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mocklistUsersResponse()
            )
        );

        $response = $this->client->listUsers();

        $this->assertArrayHasKey('Resources', $response);
    }

    #[Test]
    public function it_can_get_user_details()
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mocklistUserResponse()
            )
        );

        $user_id = '7XRNGHV24HQL4';

        $response = $this->client->showUserDetails($user_id);

        $this->assertArrayHasKey('userName', $response);
    }

    #[Test]
    public function it_can_deleta_a_user(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $user_id = '7XRNGHV24HQL4';

        $response = $this->client->deleteUser($user_id);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_create_merchant_applications(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateMerchantApplicationResponse()
            )
        );

        $response = $this->client->createMerchantApplication(
            'AGGREGATOR',
            [
                'https://example.com/callback',
                'https://example.com/callback2',
            ],
            [
                'facilitator@example.com',
                'merchant@example.com',
            ],
            'WDJJHEBZ4X2LY',
            'some-open-id'
        );

        $this->assertArrayHasKey('client_name', $response);
        $this->assertArrayHasKey('contacts', $response);
        $this->assertArrayHasKey('redirect_uris', $response);
    }

    #[Test]
    public function it_can_set_account_properties(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client('')
        );

        $response = $this->client->setAccountProperties($this->mockSetAccountPropertiesParams());

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_disable_account_properties(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateOrdersResponse()
            )
        );

        $response = $this->client->disableAccountProperties();

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_get_client_token(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetClientTokenResponse()
            )
        );

        $response = $this->client->getClientToken();

        $this->assertArrayHasKey('client_token', $response);
    }

    #[Test]
    public function it_can_create_orders(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateOrdersResponse()
            )
        );

        $filters = $this->createOrderParams();

        $response = $this->client->createOrder($filters);

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_update_orders(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateOrdersResponse()
            )
        );

        $order_id = '5O190127TN364715T';
        $filters = $this->updateOrderParams();

        $response = $this->client->updateOrder($order_id, $filters);

        $this->assertNotEmpty($response);
    }

    #[Test]
    public function it_can_get_order_details(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockOrderDetailsResponse()
            )
        );

        $order_id = '5O190127TN364715T';
        $response = $this->client->showOrderDetails($order_id);

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('intent', $response);
        $this->assertArrayHasKey('payment_source', $response);
        $this->assertArrayHasKey('purchase_units', $response);
        $this->assertArrayHasKey('create_time', $response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_authorize_payment_for_an_order(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockOrderPaymentAuthorizedResponse()
            )
        );

        $order_id = '5O190127TN364715T';
        $response = $this->client->authorizePaymentOrder($order_id);

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('payer', $response);
        $this->assertArrayHasKey('purchase_units', $response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_create_partner_referral(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePartnerReferralsResponse()
            )
        );

        $expectedParams = $this->mockCreatePartnerReferralParams();

        $response = $this->client->createPartnerReferral($expectedParams);

        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_get_referral_details(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockShowReferralDataResponse()
            )
        );

        $partner_referral_id = 'ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=';

        $response = $this->client->showReferralData($partner_referral_id);

        $this->assertArrayHasKey('partner_referral_id', $response);
        $this->assertArrayHasKey('referral_data', $response);
    }

    #[Test]
    public function it_can_list_seller_tracking_information(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListSellerTrackingInformationResponse()
            )
        );

        $partner_id = 'U6E69K99P3G88';
        $tracking_id = 'merchantref1';

        $response = $this->client->listSellerTrackingInformation($partner_id, $tracking_id);

        $this->assertArrayHasKey('merchant_id', $response);
        $this->assertArrayHasKey('tracking_id', $response);
    }

    #[Test]
    public function it_can_show_seller_status(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockShowSellerStatusResponse()
            )
        );

        $partner_id = 'U6E69K99P3G88';
        $merchant_id = '8LQLM2ML4ZTYU';

        $response = $this->client->showSellerStatus($partner_id, $merchant_id);

        $this->assertArrayHasKey('merchant_id', $response);
    }

    #[Test]
    public function it_can_list_merchant_credentials(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListMerchantCredentialsResponse()
            )
        );

        $partner_id = 'U6E69K99P3G88';

        $response = $this->client->listMerchantCredentials($partner_id);

        $this->assertArrayHasKey('client_id', $response);
        $this->assertArrayHasKey('payer_id', $response);
    }

    #[Test]
    public function it_can_list_web_experience_profiles(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListWebProfilesResponse()
            )
        );

        $response = $this->client->listWebExperienceProfiles();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', collect($response)->first());
    }

    #[Test]
    public function it_can_create_web_experience_profile(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockWebProfileResponse()
            )
        );

        $expectedParams = $this->mockCreateWebProfileParams();

        $response = $this->client->setRequestHeader('PayPal-Request-Id', 'some-request-id')->createWebExperienceProfile($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('name', $response);
    }

    #[Test]
    public function it_can_delete_web_experience_profile(): void
    {
        $expectedResponse = '';

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $expectedParams = 'XP-A88A-LYLW-8Y3X-E5ER';

        $response = $this->client->deleteWebExperienceProfile($expectedParams);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_partially_update_web_experience_profile(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->partiallyUpdateWebProfileParams();

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->patchWebExperienceProfile('XP-A88A-LYLW-8Y3X-E5ER', $expectedParams);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_fully_update_web_experience_profile(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->updateWebProfileParams();

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->updateWebExperienceProfile('XP-A88A-LYLW-8Y3X-E5ER', $expectedParams);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_get_web_experience_profile_details(): void
    {
        $expectedResponse = $this->mockWebProfileResponse();

        $expectedParams = 'XP-A88A-LYLW-8Y3X-E5ER';

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->showWebExperienceProfileDetails($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('name', $response);
    }

    #[Test]
    public function it_can_capture_payment_for_an_order(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockOrderPaymentCapturedResponse()
            )
        );

        $order_id = '5O190127TN364715T';
        $response = $this->client->capturePaymentOrder($order_id);

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('payer', $response);
        $this->assertArrayHasKey('purchase_units', $response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_show_details_for_an_authorized_payment(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetAuthorizedPaymentDetailsResponse()
            )
        );

        $response = $this->client->showAuthorizedPaymentDetails('0VF52814937998046');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_capture_an_authorized_payment(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCaptureAuthorizedPaymentResponse()
            )
        );

        $response = $this->client->captureAuthorizedPayment(
            '0VF52814937998046',
            'INVOICE-123',
            10.99,
            'Payment is due'
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_reauthorize_an_authorized_payment(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockReAuthorizeAuthorizedPaymentResponse()
            )
        );

        $response = $this->client->reAuthorizeAuthorizedPayment('0VF52814937998046', 10.99);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_void_an_authorized_payment(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->voidAuthorizedPayment('0VF52814937998046');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_show_details_for_a_captured_payment(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetCapturedPaymentDetailsResponse()
            )
        );

        $response = $this->client->showCapturedPaymentDetails('2GG279541U471931P');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_refund_a_captured_payment(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockRefundCapturedPaymentResponse()
            )
        );

        $response = $this->client->refundCapturedPayment(
            '2GG279541U471931P',
            'INVOICE-123',
            10.99,
            'Defective product'
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_show_details_for_a_refund(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetRefundDetailsResponse()
            )
        );

        $response = $this->client->showRefundDetails('1JU08902781691411');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_create_batch_payout(): void
    {
        $expectedResponse = $this->mockCreateBatchPayoutResponse();

        $expectedParams = $this->mockCreateBatchPayoutParams();

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->createBatchPayout($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('batch_header', $response);
    }

    #[Test]
    public function it_can_show_batch_payout_details(): void
    {
        $expectedResponse = $this->showBatchPayoutResponse();

        $expectedParams = 'FYXMPQTX4JC9N';

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->showBatchPayoutDetails($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('batch_header', $response);
        $this->assertArrayHasKey('items', $response);
    }

    #[Test]
    public function it_can_show_batch_payout_item_details(): void
    {
        $expectedResponse = $this->showBatchPayoutItemResponse();

        $expectedParams = '8AELMXH8UB2P8';

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->showPayoutItemDetails($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('payout_item_id', $response);
        $this->assertArrayHasKey('payout_batch_id', $response);
        $this->assertArrayHasKey('payout_item', $response);
    }

    #[Test]
    public function it_can_cancel_unclaimed_batch_payout_item(): void
    {
        $expectedResponse = $this->mockCancelUnclaimedBatchItemResponse();

        $expectedParams = '8AELMXH8UB2P8';

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->cancelUnclaimedPayoutItem($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('payout_item_id', $response);
        $this->assertArrayHasKey('payout_batch_id', $response);
        $this->assertArrayHasKey('payout_item', $response);
    }

    #[Test]
    public function it_can_create_referenced_batch_payout(): void
    {
        $expectedResponse = $this->mockCreateReferencedBatchPayoutResponse();

        $expectedParams = $this->mockCreateReferencedBatchPayoutParams();

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->setRequestHeaders([
            'PayPal-Request-Id'             => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ])->createReferencedBatchPayout($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_list_items_referenced_in_batch_payout(): void
    {
        $expectedResponse = $this->mockShowReferencedBatchPayoutResponse();

        $expectedParams = 'KHbwO28lWlXwi2IlToJ2IYNG4juFv6kpbFx4J9oQ5Hb24RSp96Dk5FudVHd6v4E=';

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->listItemsReferencedInBatchPayout($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_create_referenced_batch_payout_item(): void
    {
        $expectedResponse = $this->mockCreateReferencedBatchPayoutItemResponse();

        $expectedParams = $this->mockCreateReferencedBatchPayoutItemParams();

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->setRequestHeaders([
            'PayPal-Request-Id'             => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ])->createReferencedBatchPayoutItem($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function it_can_show_referenced_payout_item_details(): void
    {
        $expectedResponse = $this->mockShowReferencedBatchPayoutItemResponse();

        $expectedParams = 'CDZEC5MJ8R5HY';

        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->setRequestHeader('PayPal-Partner-Attribution-Id', 'some-attribution-id')
        ->showReferencedPayoutItemDetails($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('item_id', $response);
        $this->assertArrayHasKey('reference_id', $response);
    }

    #[Test]
    public function it_can_list_transactions(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListTransactionsResponse()
            )
        );

        $filters = [
            'start_date'    => Carbon::now()->toIso8601String(),
            'end_date'      => Carbon::now()->subDays(30)->toIso8601String(),
        ];

        $response = $this->client->listTransactions($filters);

        $this->assertArrayHasKey('transaction_details', $response);
        $this->assertGreaterThan(0, sizeof($response['transaction_details']));
    }

    #[Test]
    public function it_can_list_account_balances(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListBalancesResponse()
            )
        );

        $date = Carbon::now()->subDays(30)->toIso8601String();

        $response = $this->client->listBalances($date);

        $this->assertNotEmpty($response);
    }

    #[Test]
    public function it_can_list_account_balances_for_a_different_currency(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListBalancesResponse()
            )
        );

        $date = Carbon::now()->subDays(30)->toIso8601String();

        $response = $this->client->listBalances($date, 'EUR');

        $this->assertNotEmpty($response);
    }

    #[Test]
    public function it_can_create_a_subscription(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateSubscriptionResponse()
            )
        );

        $expectedParams = $this->mockCreateSubscriptionParams();

        $response = $this->client->createSubscription($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_update_a_subscription(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->mockUpdateSubscriptionParams();

        $response = $this->client->updateSubscription('I-BW452GLLEP1G', $expectedParams);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_show_details_for_a_subscription(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetSubscriptionDetailsResponse()
            )
        );

        $response = $this->client->showSubscriptionDetails('I-BW452GLLEP1G');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_activate_a_subscription(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->activateSubscription('I-BW452GLLEP1G', 'Reactivating the subscription');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_cancel_a_subscription(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->cancelSubscription('I-BW452GLLEP1G', 'Not satisfied with the service');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_suspend_a_subscription()
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->suspendSubscription('I-BW452GLLEP1G', 'Item out of stock');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_capture_payment_for_a_subscription(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->captureSubscriptionPayment('I-BW452GLLEP1G', 'Charging as the balance reached the limit', 100);

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_update_quantity_or_product_for_a_subscription(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateSubscriptionItemsResponse()
            )
        );

        $expectedParams = $this->mockUpdateSubscriptionItemsParams();

        $response = $this->client->reviseSubscription('I-BW452GLLEP1G', $expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('plan_id', $response);
    }

    #[Test]
    public function it_can_list_transactions_for_a_subscription(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListSubscriptionTransactionsResponse()
            )
        );

        $response = $this->client->listSubscriptionTransactions('I-BW452GLLEP1G', '2018-01-21T07:50:20.940Z', '2018-08-22T07:50:20.940Z');

        $this->assertNotEmpty($response);
        $this->assertEquals($response, $this->mockListSubscriptionTransactionsResponse());
    }

    #[Test]
    public function it_can_list_tracking_details(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetTrackingDetailsResponse()
            )
        );

        $response = $this->client->listTrackingDetails('8MC585209K746392H-443844607820');

        $this->assertNotEmpty($response);
        $this->assertEquals($response, $this->mockGetTrackingDetailsResponse());
        $this->assertArrayHasKey('transaction_id', $response);
        $this->assertArrayHasKey('tracking_number', $response);
    }

    #[Test]
    public function it_can_get_tracking_details_for_tracking_id(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetTrackingDetailsResponse()
            )
        );

        $response = $this->client->showTrackingDetails('8MC585209K746392H-443844607820');

        $this->assertNotEmpty($response);
        $this->assertEquals($response, $this->mockGetTrackingDetailsResponse());
        $this->assertArrayHasKey('tracking_number', $response);
    }

    #[Test]
    public function it_can_update_tracking_details_for_tracking_id(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->updateTrackingDetails(
            '8MC585209K746392H-443844607820',
            $this->mockUpdateTrackingDetailsParams()
        );

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_create_tracking_in_batches(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateTrackinginBatchesResponse()
            )
        );

        $expectedParams = $this->mockCreateTrackinginBatchesParams();

        $response = $this->client->addBatchTracking($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('tracker_identifiers', $response);
    }

    #[Test]
    public function it_can_create_single_tracking_for_single_transaction(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateTrackinginBatchesResponse()
            )
        );

        $expectedParams = $this->mockCreateTrackinginBatchesParams();

        $response = $this->client->addTracking($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('tracker_identifiers', $response);
    }

    #[Test]
    public function it_can_list_web_hooks_event_types(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListWebHookEventsTypesResponse()
            )
        );

        $response = $this->client->listEventTypes();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('event_types', $response);
    }

    #[Test]
    public function it_can_list_web_hooks_events(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockWebHookEventsListResponse()
            )
        );

        $response = $this->client->listEvents();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('events', $response);
    }

    #[Test]
    public function it_can_show_details_for_a_web_hooks_event(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetWebHookEventResponse()
            )
        );

        $response = $this->client->showEventDetails('8PT597110X687430LKGECATA');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_resend_notification_for_a_web_hooks_event(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockResendWebHookEventNotificationResponse()
            )
        );

        $expectedParams = ['12334456'];

        $response = $this->client->resendEventNotification('8PT597110X687430LKGECATA', $expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function it_can_create_a_web_hook(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateWebHookResponse()
            )
        );

        $response = $this->client->createWebHook(
            'https://example.com/example_webhook',
            ['PAYMENT.AUTHORIZATION.CREATED', 'PAYMENT.AUTHORIZATION.VOIDED']
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('event_types', $response);
    }

    #[Test]
    public function it_can_list_web_hooks(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListWebHookResponse()
            )
        );

        $response = $this->client->listWebHooks();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('webhooks', $response);
    }

    #[Test]
    public function it_can_delete_a_web_hook(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteWebHook('5GP028458E2496506');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_update_a_web_hook(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateWebHookResponse()
            )
        );

        $expectedParams = $this->mockUpdateWebHookParams();

        $response = $this->client->updateWebHook('0EH40505U7160970P', $expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('event_types', $response);
    }

    #[Test]
    public function it_can_show_details_for_a_web_hook(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetWebHookResponse()
            )
        );

        $response = $this->client->showWebHookDetails('0EH40505U7160970P');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('event_types', $response);
    }

    #[Test]
    public function it_can_list_events_for_web_hooks(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListWebHookEventsResponse()
            )
        );

        $response = $this->client->listWebHookEvents('0EH40505U7160970P');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('event_types', $response);
    }

    #[Test]
    public function it_can_verify_web_hook_signature(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockVerifyWebHookSignatureResponse()
            )
        );

        $expectedParams = $this->mockVerifyWebHookSignatureParams();

        $response = $this->client->verifyWebHook($expectedParams);

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('verification_status', $response);
    }

    #[Test]
    public function it_can_list_payment_methods_source_tokens(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListPaymentMethodsTokensResponse()
            )
        );

        $response = $this->client->setCustomerSource('customer_4029352050')
        ->listPaymentSourceTokens();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('payment_tokens', $response);
    }

    #[Test]
    public function it_can_show_details_for_payment_method_source_token(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePaymentMethodsTokenResponse()
            )
        );

        $response = $this->client->showPaymentSourceTokenDetails('8kk8451t');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('customer', $response);
        $this->assertArrayHasKey('payment_source', $response);
    }

    #[Test]
    public function it_can_delete_a_payment_method_source_token(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deletePaymentSourceToken('8kk8451t');

        $this->assertEmpty($response);
    }

    #[Test]
    public function it_can_show_details_for_payment_setup_token(): void
    {
        $this->client->setAccessToken([
            'access_token'  => self::$access_token,
            'token_type'    => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListPaymentSetupTokenResponse()
            )
        );

        $response = $this->client->showPaymentSetupTokenDetails('5C991763VB2781612');

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('customer', $response);
        $this->assertArrayHasKey('payment_source', $response);
    }
}
