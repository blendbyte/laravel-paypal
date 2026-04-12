<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Srmklive\PayPal\Tests\MockRequestPayloads;
use Carbon\Carbon;

uses(MockRequestPayloads::class);

beforeEach(function () {
    $this->client = new PayPalClient($this->getApiCredentials());
    $this->access_token = '';
});

// ─────────────────────────────────────────────────────────────────────────────
// Access Token
// ─────────────────────────────────────────────────────────────────────────────

describe('Access Token', function () {
    it('returns error if invalid credentials are used to get access token', function () {
        $this->client = new PayPalClient($this->getMockCredentials());
        $response = $this->client->getAccessToken();

        expect($response['error'])->toBeArray();
        expect($response)->toHaveKey('error');
    });

    it('can get access token', function () {
        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mockAccessTokenResponse(),
                $container
            )
        );
        $response = $this->client->getAccessToken();

        $this->access_token = $response['access_token'];

        expect($response)->toHaveKey('access_token');
        expect($response['access_token'])->not->toBeEmpty();

        // Verify the actual PSR-7 request that was sent carried the right auth
        // and body — this would have caught the PSR-18 migration regression.
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];

        $credentials = $this->getApiCredentials();
        $expectedBasic = 'Basic '.base64_encode(
            $credentials['sandbox']['client_id'].':'.$credentials['sandbox']['client_secret']
        );

        expect($request->getHeaderLine('Authorization'))->toBe($expectedBasic);
        expect($request->getHeaderLine('Content-Type'))->toBe('application/x-www-form-urlencoded');
        expect((string) $request->getBody())->toBe('grant_type=client_credentials');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Billing Agreements
// ─────────────────────────────────────────────────────────────────────────────

describe('Billing Agreements', function () {
    it('can create a billing agreement token', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateBillingAgreementTokenResponse()
            )
        );

        $expectedParams = $this->createBillingAgreementTokenParams();

        $response = $this->client->setRequestHeader('PayPal-Request-Id', 'some-request-id')->createBillingAgreementToken($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('token_id');
    });

    it('can show billing agreement token details', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetBillingAgreementTokenResponse()
            )
        );

        $response = $this->client->getBillingAgreementTokenDetails('BA-8A802366G0648845Y');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('token_id');
    });

    it('can create a billing agreement', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateBillingAgreementResponse()
            )
        );

        $response = $this->client->setRequestHeader('PayPal-Request-Id', 'some-request-id')->createBillingAgreement('BA-8A802366G0648845Y');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can update a billing agreement', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updateBillingAgreementParams();

        $response = $this->client->updateBillingAgreement('BA-8A802366G0648845Y', $expectedParams);

        expect($response)->toBeEmpty();
    });

    it('can show billing agreement details', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockShowBillingAgreementResponse()
            )
        );

        $response = $this->client->showBillingAgreementDetails('BA-8A802366G0648845Y');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can cancel a billing agreement', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->cancelBillingAgreement('BA-8A802366G0648845Y');

        expect($response)->toBeEmpty();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Billing Plans
// ─────────────────────────────────────────────────────────────────────────────

describe('Billing Plans', function () {
    it('can create a billing plan', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePlansResponse()
            )
        );

        $expectedParams = $this->createPlanParams();

        $response = $this->client->setRequestHeader('PayPal-Request-Id', 'some-request-id')->createPlan($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can list billing plans', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListPlansResponse()
            )
        );

        $response = $this->client->listPlans();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('plans');
    });

    it('can update a billing plan', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updatePlanParams();

        $response = $this->client->updatePlan('P-7GL4271244454362WXNWU5NQ', $expectedParams);

        expect($response)->toBeEmpty();
    });

    it('can show details for a billing plan', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetPlansResponse()
            )
        );

        $response = $this->client->showPlanDetails('P-5ML4271244454362WXNWU5NQ');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can activate a billing plan', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->activatePlan('P-7GL4271244454362WXNWU5NQ');

        expect($response)->toBeEmpty();
    });

    it('can deactivate a billing plan', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deactivatePlan('P-7GL4271244454362WXNWU5NQ');

        expect($response)->toBeEmpty();
    });

    it('can update pricing for a billing plan', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updatePlanPricingParams();

        $response = $this->client->updatePlanPricing('P-2UF78835G6983425GLSM44MA', $expectedParams);

        expect($response)->toBeEmpty();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Catalog Products
// ─────────────────────────────────────────────────────────────────────────────

describe('Catalog Products', function () {
    it('can list products', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListCatalogProductsResponse()
            )
        );

        $response = $this->client->listProducts();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('products');
    });

    it('can create a product', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateCatalogProductsResponse()
            )
        );

        $expectedParams = $this->createProductParams();

        $response = $this->client->setRequestHeader('PayPal-Request-Id', 'product-request-'.time())->createProduct($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can update a product', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updateProductParams();

        $response = $this->client->updateProduct('PROD-XYAB12ABSB7868434', $expectedParams);

        expect($response)->toBeEmpty();
    });

    it('can get details for a product', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetCatalogProductsResponse()
            )
        );

        $response = $this->client->showProductDetails('PROD-XYAB12ABSB7868434');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Disputes
// ─────────────────────────────────────────────────────────────────────────────

describe('Disputes', function () {
    it('can acknowledge item is returned for raised dispute', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can list disputes', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListDisputesResponse()
            )
        );

        $response = $this->client->listDisputes();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('items');
    });

    it('can partially update a dispute', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->updateDisputeParams();

        $response = $this->client->updateDispute('PP-D-27803', $expectedParams);

        expect($response)->toBeEmpty();
    });

    it('can get details for a dispute', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetDisputesResponse()
            )
        );

        $response = $this->client->showDisputeDetails('PP-D-4012');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('dispute_id');
    });

    it('can provide evidence for a dispute claim', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mockAcceptDisputesClaimResponse(),
                $container
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');

        // Regression guard: a leading '/' on the endpoint produces a double-slash
        // URL (e.g. https://api-m.sandbox.paypal.com//v1/...) which PayPal rejects.
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        expect((string) $request->getUri())->not->toContain('//v1/');
        expect((string) $request->getUri())->toContain('/v1/customer/disputes/PP-D-27803/provide-evidence');
    });

    it('throws exception if invalid file as evidence is provided for a dispute claim', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect(fn () => $this->client->provideDisputeEvidence('PP-D-27803', $mockFiles))->toThrow(Exception::class);
    });

    it('throws exception if file size as evidence exceeds per file limit for a dispute claim')
        ->todo('Requires a >10MB test file not included in the repo');

    it('throws exception if file size as evidence exceeds overall limit for a dispute claim')
        ->todo('Requires >50MB of test files not included in the repo');

    it('can offer to resolve dispute claim', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can escalate dispute claim', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can accept dispute claim', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mockAcceptDisputesClaimResponse(),
                $container
            )
        );

        $response = $this->client->acceptDisputeClaim(
            'PP-D-27803',
            'Full refund to the customer.'
        );

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');

        // Default claim type must be REFUND when none is supplied.
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        $body = json_decode((string) $request->getBody(), true);
        expect($body['accept_claim_type'])->toBe('REFUND');
    });

    it('acceptDisputeClaim allows caller to override accept_claim_type', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mockAcceptDisputesClaimResponse(),
                $container
            )
        );

        $this->client->acceptDisputeClaim(
            'PP-D-27803',
            'Sending replacement item.',
            ['accept_claim_type' => 'MERCHANDISE']
        );

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        $body = json_decode((string) $request->getBody(), true);
        expect($body['accept_claim_type'])->toBe('MERCHANDISE');
        expect($body['note'])->toBe('Sending replacement item.');
    });

    it('can accept dispute offer resolution', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can update dispute status', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can settle dispute', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can decline dispute offer resolution', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can send a message about a dispute', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSendDisputeMessageResponse()
            )
        );

        $response = $this->client->sendDisputeMessage(
            'PP-000-000-651-454',
            'I have shipped the item. Tracking number: 1234567890.'
        );

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Invoices
// ─────────────────────────────────────────────────────────────────────────────

describe('Invoices', function () {
    it('can generate unique invoice number', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGenerateInvoiceNumberResponse()
            )
        );

        $response = $this->client->generateInvoiceNumber();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('invoice_number');
    });

    it('can create a draft invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateInvoicesResponse()
            )
        );

        $expectedParams = $this->createInvoiceParams();

        $response = $this->client->createInvoice($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can list invoices', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListInvoicesResponse()
            )
        );

        $response = $this->client->listInvoices();

        expect($response)->toHaveKey('total_pages');
        expect($response)->toHaveKey('total_items');
    });

    it('can delete an invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteInvoice('INV2-Z56S-5LLA-Q52L-CPZ5');

        expect($response)->toBeEmpty();
    });

    it('can update an invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateInvoicesResponse()
            )
        );

        $expectedParams = $this->updateInvoiceParams();

        $response = $this->client->updateInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', $expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can show details for an invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetInvoicesResponse()
            )
        );

        $response = $this->client->showInvoiceDetails('INV2-Z56S-5LLA-Q52L-CPZ5');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can cancel an invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->toBeEmpty();
    });

    it('can generate qr code for invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGenerateInvoiceQRCodeResponse()
            )
        );

        $response = $this->client->generateQRCodeInvoice('INV2-Z56S-5LLA-Q52L-CPZ5');

        expect($response)->not->toBeEmpty();
    });

    it('can register payment for invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockInvoiceRegisterPaymentResponse()
            )
        );

        $response = $this->client->registerPaymentInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', '2018-05-01', 'BANK_TRANSFER', 10.00);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('payment_id');
    });

    it('can delete payment for invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteExternalPaymentInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 'EXTR-86F38350LX4353815');

        expect($response)->toBeEmpty();
    });

    it('can refund payment for invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockInvoiceRefundPaymentResponse()
            )
        );

        $response = $this->client->refundInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', '2018-05-01', 'BANK_TRANSFER', 5.00);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('refund_id');
    });

    it('can delete refund for invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteRefundInvoice('INV2-Z56S-5LLA-Q52L-CPZ5', 'EXTR-2LG703375E477444T');

        expect($response)->toBeEmpty();
    });

    it('can send an invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->toBeEmpty();
    });

    it('can send reminder for an invoice', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->toBeEmpty();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Invoice Templates
// ─────────────────────────────────────────────────────────────────────────────

describe('Invoice Templates', function () {
    it('can create invoice template', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateInvoiceTemplateResponse()
            )
        );

        $expectedParams = $this->mockCreateInvoiceTemplateParams();

        $response = $this->client->createInvoiceTemplate($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can list invoice templates', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListInvoiceTemplateResponse()
            )
        );

        $response = $this->client->listInvoiceTemplates();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('templates');
    });

    it('can delete an invoice template', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteInvoiceTemplate('TEMP-19V05281TU309413B');

        expect($response)->toBeEmpty();
    });

    it('can update an invoice template', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateInvoiceTemplateResponse()
            )
        );

        $expectedParams = $this->mockUpdateInvoiceTemplateParams();

        $response = $this->client->updateInvoiceTemplate('TEMP-19V05281TU309413B', $expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can get details for an invoice template', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetInvoiceTemplateResponse()
            )
        );

        $response = $this->client->showInvoiceTemplateDetails('TEMP-19V05281TU309413B');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Invoice Search
// ─────────────────────────────────────────────────────────────────────────────

describe('Invoice Search', function () {
    it('can search invoices', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $response = $this->client->searchInvoices();

        expect($response)->toHaveKey('total_pages');
        expect($response)->toHaveKey('total_items');
    });

    it('can search invoices with custom filters', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->toHaveKey('total_pages');
        expect($response)->toHaveKey('total_items');
        expect($response)->toHaveKey('items');
    });

    it('throws exception on search invoices with invalid status', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        expect(fn () => $this->client->addInvoiceFilterByInvoiceStatus(['DECLINED'])->searchInvoices())->toThrow(Exception::class);
    });

    it('throws exception on search invoices with invalid amount ranges', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $filters = $this->invoiceSearchParams();

        expect(fn () => $this->client->addInvoiceFilterByAmountRange(50, 30)->searchInvoices())->toThrow(Exception::class);
    });

    it('throws exception on search invoices with invalid date ranges', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $filters = $this->invoiceSearchParams();

        expect(fn () => $this->client->addInvoiceFilterByDateRange('2018-07-01', '2018-06-21', 'invoice_date')->searchInvoices())->toThrow(Exception::class);
    });

    it('normalises non-ISO date strings to Y-m-d format in date range filter', function () {
        // Regression: Carbon::parse() was used to validate ordering but the raw
        // input strings were stored verbatim. Passing "January 5 2024" or "2024-1-5"
        // would store those literals and send them to the PayPal API unchanged.
        $this->client->addInvoiceFilterByDateRange('January 1 2018', 'June 21 2018', 'invoice_date');

        $prop = (new ReflectionClass($this->client))->getProperty('invoice_search_filters');
        $filters = $prop->getValue($this->client);

        expect($filters['invoice_date_range']['start'])->toBe('2018-01-01');
        expect($filters['invoice_date_range']['end'])->toBe('2018-06-21');
    });

    it('throws exception on search invoices with invalid date range type', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockSearchInvoicesResponse()
            )
        );

        $filters = $this->invoiceSearchParams();

        expect(fn () => $this->client->addInvoiceFilterByDateRange('2018-06-01', '2018-06-21', 'declined_date')->searchInvoices())->toThrow(Exception::class);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Identity & Users
// ─────────────────────────────────────────────────────────────────────────────

describe('Identity & Users', function () {
    it('can get user profile details', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockShowProfileInfoResponse()
            )
        );

        $response = $this->client->showProfileInfo();

        expect($response)->toHaveKey('address');
    });

    it('can get list users', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mocklistUsersResponse(),
                $container
            )
        );

        $response = $this->client->listUsers();

        expect($response)->toHaveKey('Resources');

        // Regression: the default filter value must appear verbatim (no spaces to encode).
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        $url = (string) $request->getUri();
        expect($url)->toContain('filter=userName');
    });

    it('URL-encodes the SCIM filter when it contains spaces or special characters', function () {
        // Regression: bare string interpolation left spaces/quotes raw in the URL,
        // which breaks HTTP clients that enforce RFC 3986 and confuses some proxies.
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mocklistUsersResponse(),
                $container
            )
        );

        $this->client->listUsers('userName eq "bjensen"');

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        $url = (string) $request->getUri();

        // Must be percent-encoded, not raw spaces or quotes.
        expect($url)->toContain('filter=userName%20eq%20%22bjensen%22');
        expect($url)->not->toContain('filter=userName eq');
    });

    it('can get user details', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mocklistUserResponse()
            )
        );

        $user_id = '7XRNGHV24HQL4';

        $response = $this->client->showUserDetails($user_id);

        expect($response)->toHaveKey('userName');
    });

    it('can deleta a user', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $user_id = '7XRNGHV24HQL4';

        $response = $this->client->deleteUser($user_id);

        expect($response)->toBeEmpty();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Orders
// ─────────────────────────────────────────────────────────────────────────────

describe('Orders', function () {
    it('can create orders', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateOrdersResponse()
            )
        );

        $filters = $this->createOrderParams();

        $response = $this->client->createOrder($filters);

        expect($response)->toHaveKey('status');
        expect($response)->toHaveKey('id');
        expect($response)->toHaveKey('links');
    });

    it('can update orders', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateOrdersResponse()
            )
        );

        $order_id = '5O190127TN364715T';
        $filters = $this->updateOrderParams();

        $response = $this->client->updateOrder($order_id, $filters);

        expect($response)->not->toBeEmpty();
    });

    it('can get order details', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockOrderDetailsResponse()
            )
        );

        $order_id = '5O190127TN364715T';
        $response = $this->client->showOrderDetails($order_id);

        expect($response)->toHaveKey('status');
        expect($response)->toHaveKey('id');
        expect($response)->toHaveKey('intent');
        expect($response)->toHaveKey('payment_source');
        expect($response)->toHaveKey('purchase_units');
        expect($response)->toHaveKey('create_time');
        expect($response)->toHaveKey('links');
    });

    it('can authorize payment for an order', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockOrderPaymentAuthorizedResponse()
            )
        );

        $order_id = '5O190127TN364715T';
        $response = $this->client->authorizePaymentOrder($order_id);

        expect($response)->toHaveKey('status');
        expect($response)->toHaveKey('id');
        expect($response)->toHaveKey('payer');
        expect($response)->toHaveKey('purchase_units');
        expect($response)->toHaveKey('links');
    });

    it('can capture payment for an order', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockOrderPaymentCapturedResponse()
            )
        );

        $order_id = '5O190127TN364715T';
        $response = $this->client->capturePaymentOrder($order_id);

        expect($response)->toHaveKey('status');
        expect($response)->toHaveKey('id');
        expect($response)->toHaveKey('payer');
        expect($response)->toHaveKey('purchase_units');
        expect($response)->toHaveKey('links');
    });

    it('can add tracking for an order', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateOrdersResponse()
            )
        );

        $response = $this->client->addTrackingForOrder('5O190127TN364715T', $this->addTrackingForOrderParams());

        expect($response)->not->toBeNull();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Partner Referrals
// ─────────────────────────────────────────────────────────────────────────────

describe('Partner Referrals', function () {
    it('can create merchant applications', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->toHaveKey('client_name');
        expect($response)->toHaveKey('contacts');
        expect($response)->toHaveKey('redirect_uris');
    });

    it('can set account properties', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client('')
        );

        $response = $this->client->setAccountProperties($this->mockSetAccountPropertiesParams());

        expect($response)->toBeEmpty();
    });

    it('can disable account properties', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateOrdersResponse()
            )
        );

        $response = $this->client->disableAccountProperties();

        expect($response)->toBeEmpty();
    });

    it('can get client token', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetClientTokenResponse()
            )
        );

        $response = $this->client->getClientToken();

        expect($response)->toHaveKey('client_token');
    });

    it('can generate client token for fastlane', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetClientTokenResponse()
            )
        );

        $response = $this->client->generateClientToken();

        expect($response)->toHaveKey('client_token');
    });

    it('can create partner referral', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePartnerReferralsResponse()
            )
        );

        $expectedParams = $this->mockCreatePartnerReferralParams();

        $response = $this->client->createPartnerReferral($expectedParams);

        expect($response)->toHaveKey('links');
    });

    it('can get referral details', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockShowReferralDataResponse()
            )
        );

        $partner_referral_id = 'ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=';

        $response = $this->client->showReferralData($partner_referral_id);

        expect($response)->toHaveKey('partner_referral_id');
        expect($response)->toHaveKey('referral_data');
    });

    it('can list seller tracking information', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListSellerTrackingInformationResponse()
            )
        );

        $partner_id = 'U6E69K99P3G88';
        $tracking_id = 'merchantref1';

        $response = $this->client->listSellerTrackingInformation($partner_id, $tracking_id);

        expect($response)->toHaveKey('merchant_id');
        expect($response)->toHaveKey('tracking_id');
    });

    it('can show seller status', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockShowSellerStatusResponse()
            )
        );

        $partner_id = 'U6E69K99P3G88';
        $merchant_id = '8LQLM2ML4ZTYU';

        $response = $this->client->showSellerStatus($partner_id, $merchant_id);

        expect($response)->toHaveKey('merchant_id');
    });

    it('can list merchant credentials', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListMerchantCredentialsResponse()
            )
        );

        $partner_id = 'U6E69K99P3G88';

        $response = $this->client->listMerchantCredentials($partner_id);

        expect($response)->toHaveKey('client_id');
        expect($response)->toHaveKey('payer_id');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Web Experience Profiles
// ─────────────────────────────────────────────────────────────────────────────

describe('Web Experience Profiles', function () {
    it('can list web experience profiles', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListWebProfilesResponse()
            )
        );

        $response = $this->client->listWebExperienceProfiles();

        expect($response)->not->toBeEmpty();
        expect(collect($response)->first())->toHaveKey('id');
    });

    it('can create web experience profile', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockWebProfileResponse()
            )
        );

        $expectedParams = $this->mockCreateWebProfileParams();

        $response = $this->client->setRequestHeader('PayPal-Request-Id', 'some-request-id')->createWebExperienceProfile($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('name');
    });

    it('can delete web experience profile', function () {
        $expectedResponse = '';

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $expectedParams = 'XP-A88A-LYLW-8Y3X-E5ER';

        $response = $this->client->deleteWebExperienceProfile($expectedParams);

        expect($response)->toBeEmpty();
    });

    it('can partially update web experience profile', function () {
        $expectedResponse = '';

        $expectedParams = $this->partiallyUpdateWebProfileParams();

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->patchWebExperienceProfile('XP-A88A-LYLW-8Y3X-E5ER', $expectedParams);

        expect($response)->toBeEmpty();
    });

    it('can fully update web experience profile', function () {
        $expectedResponse = '';

        $expectedParams = $this->updateWebProfileParams();

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->updateWebExperienceProfile('XP-A88A-LYLW-8Y3X-E5ER', $expectedParams);

        expect($response)->toBeEmpty();
    });

    it('can get web experience profile details', function () {
        $expectedResponse = $this->mockWebProfileResponse();

        $expectedParams = 'XP-A88A-LYLW-8Y3X-E5ER';

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->showWebExperienceProfileDetails($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('name');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Payments (Authorizations, Captures & Refunds)
// ─────────────────────────────────────────────────────────────────────────────

describe('Payments', function () {
    it('can show details for an authorized payment', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetAuthorizedPaymentDetailsResponse()
            )
        );

        $response = $this->client->showAuthorizedPaymentDetails('0VF52814937998046');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can capture an authorized payment', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can reauthorize an authorized payment', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockReAuthorizeAuthorizedPaymentResponse()
            )
        );

        $response = $this->client->reAuthorizeAuthorizedPayment('0VF52814937998046', 10.99);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can void an authorized payment', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->voidAuthorizedPayment('0VF52814937998046');

        expect($response)->toBeEmpty();
    });

    it('can show details for a captured payment', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetCapturedPaymentDetailsResponse()
            )
        );

        $response = $this->client->showCapturedPaymentDetails('2GG279541U471931P');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can refund a captured payment', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can show details for a refund', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetRefundDetailsResponse()
            )
        );

        $response = $this->client->showRefundDetails('1JU08902781691411');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Payouts
// ─────────────────────────────────────────────────────────────────────────────

describe('Payouts', function () {
    it('can create batch payout', function () {
        $expectedResponse = $this->mockCreateBatchPayoutResponse();

        $expectedParams = $this->mockCreateBatchPayoutParams();

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->createBatchPayout($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('batch_header');
    });

    it('can show batch payout details', function () {
        $expectedResponse = $this->showBatchPayoutResponse();

        $expectedParams = 'FYXMPQTX4JC9N';

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->showBatchPayoutDetails($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('batch_header');
        expect($response)->toHaveKey('items');
    });

    it('can show batch payout item details', function () {
        $expectedResponse = $this->showBatchPayoutItemResponse();

        $expectedParams = '8AELMXH8UB2P8';

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->showPayoutItemDetails($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('payout_item_id');
        expect($response)->toHaveKey('payout_batch_id');
        expect($response)->toHaveKey('payout_item');
    });

    it('can cancel unclaimed batch payout item', function () {
        $expectedResponse = $this->mockCancelUnclaimedBatchItemResponse();

        $expectedParams = '8AELMXH8UB2P8';

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->cancelUnclaimedPayoutItem($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('payout_item_id');
        expect($response)->toHaveKey('payout_batch_id');
        expect($response)->toHaveKey('payout_item');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Referenced Payouts
// ─────────────────────────────────────────────────────────────────────────────

describe('Referenced Payouts', function () {
    it('can create referenced batch payout', function () {
        $expectedResponse = $this->mockCreateReferencedBatchPayoutResponse();

        $expectedParams = $this->mockCreateReferencedBatchPayoutParams();

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->setRequestHeaders([
            'PayPal-Request-Id' => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ])->createReferencedBatchPayout($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can list items referenced in batch payout', function () {
        $expectedResponse = $this->mockShowReferencedBatchPayoutResponse();

        $expectedParams = 'KHbwO28lWlXwi2IlToJ2IYNG4juFv6kpbFx4J9oQ5Hb24RSp96Dk5FudVHd6v4E=';

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->listItemsReferencedInBatchPayout($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can create referenced batch payout item', function () {
        $expectedResponse = $this->mockCreateReferencedBatchPayoutItemResponse();

        $expectedParams = $this->mockCreateReferencedBatchPayoutItemParams();

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->setRequestHeaders([
            'PayPal-Request-Id' => 'some-request-id',
            'PayPal-Partner-Attribution-Id' => 'some-attribution-id',
        ])->createReferencedBatchPayoutItem($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('links');
    });

    it('can show referenced payout item details', function () {
        $expectedResponse = $this->mockShowReferencedBatchPayoutItemResponse();

        $expectedParams = 'CDZEC5MJ8R5HY';

        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client($expectedResponse)
        );

        $response = $this->client->setRequestHeader('PayPal-Partner-Attribution-Id', 'some-attribution-id')
            ->showReferencedPayoutItemDetails($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('item_id');
        expect($response)->toHaveKey('reference_id');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Reporting
// ─────────────────────────────────────────────────────────────────────────────

describe('Reporting', function () {
    it('can list transactions', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListTransactionsResponse()
            )
        );

        $filters = [
            'start_date' => Carbon::now()->toIso8601String(),
            'end_date' => Carbon::now()->subDays(30)->toIso8601String(),
        ];

        $response = $this->client->listTransactions($filters);

        expect($response)->toHaveKey('transaction_details');
        expect(count($response['transaction_details']))->toBeGreaterThan(0);
    });

    it('can list account balances', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListBalancesResponse()
            )
        );

        $date = Carbon::now()->subDays(30)->toIso8601String();

        $response = $this->client->listBalances($date);

        expect($response)->not->toBeEmpty();
    });

    it('can list account balances for a different currency', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListBalancesResponse()
            )
        );

        $date = Carbon::now()->subDays(30)->toIso8601String();

        $response = $this->client->listBalances($date, 'EUR');

        expect($response)->not->toBeEmpty();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Subscriptions
// ─────────────────────────────────────────────────────────────────────────────

describe('Subscriptions', function () {
    it('can create a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateSubscriptionResponse()
            )
        );

        $expectedParams = $this->mockCreateSubscriptionParams();

        $response = $this->client->createSubscription($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can update a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $expectedParams = $this->mockUpdateSubscriptionParams();

        $response = $this->client->updateSubscription('I-BW452GLLEP1G', $expectedParams);

        expect($response)->toBeEmpty();
    });

    it('can show details for a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetSubscriptionDetailsResponse()
            )
        );

        $response = $this->client->showSubscriptionDetails('I-BW452GLLEP1G');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can activate a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->activateSubscription('I-BW452GLLEP1G', 'Reactivating the subscription');

        expect($response)->toBeEmpty();
    });

    it('can cancel a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->cancelSubscription('I-BW452GLLEP1G', 'Not satisfied with the service');

        expect($response)->toBeEmpty();
    });

    it('can suspend a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->suspendSubscription('I-BW452GLLEP1G', 'Item out of stock');

        expect($response)->toBeEmpty();
    });

    it('can capture payment for a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->captureSubscriptionPayment('I-BW452GLLEP1G', 'Charging as the balance reached the limit', 100);

        expect($response)->toBeEmpty();
    });

    it('can update quantity or product for a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateSubscriptionItemsResponse()
            )
        );

        $expectedParams = $this->mockUpdateSubscriptionItemsParams();

        $response = $this->client->reviseSubscription('I-BW452GLLEP1G', $expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('plan_id');
    });

    it('can list transactions for a subscription', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListSubscriptionTransactionsResponse()
            )
        );

        $response = $this->client->listSubscriptionTransactions('I-BW452GLLEP1G', '2018-01-21T07:50:20.940Z', '2018-08-22T07:50:20.940Z');

        expect($response)->not->toBeEmpty();
        expect($response)->toBe($this->mockListSubscriptionTransactionsResponse());
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Tracking
// ─────────────────────────────────────────────────────────────────────────────

describe('Tracking', function () {
    it('can list tracking details', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mockGetTrackingDetailsResponse(),
                $container
            )
        );

        $response = $this->client->listTrackingDetails('8MC585209K746392H-443844607820');

        expect($response)->not->toBeEmpty();
        expect($response)->toBe($this->mockGetTrackingDetailsResponse());
        expect($response)->toHaveKey('transaction_id');
        expect($response)->toHaveKey('tracking_number');

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        $url = (string) $request->getUri();
        expect($url)->toContain('transaction_id=8MC585209K746392H-443844607820');
    });

    it('URL-encodes tracking_number when it contains special characters', function () {
        // Regression: bare string interpolation left special characters raw in the URL.
        // Some carrier tracking numbers include spaces or other non-URL-safe characters.
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mockGetTrackingDetailsResponse(),
                $container
            )
        );

        $this->client->listTrackingDetails('8MC585209K746392H', 'TRACK 123+456');

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        $url = (string) $request->getUri();
        expect($url)->toContain('tracking_number=TRACK+123%2B456');
        expect($url)->not->toContain('tracking_number=TRACK 123');
    });

    it('can get tracking details for tracking id', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetTrackingDetailsResponse()
            )
        );

        $response = $this->client->showTrackingDetails('8MC585209K746392H-443844607820');

        expect($response)->not->toBeEmpty();
        expect($response)->toBe($this->mockGetTrackingDetailsResponse());
        expect($response)->toHaveKey('tracking_number');
    });

    it('can update tracking details for tracking id', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->updateTrackingDetails(
            '8MC585209K746392H-443844607820',
            $this->mockUpdateTrackingDetailsParams()
        );

        expect($response)->toBeEmpty();
    });

    it('can create tracking in batches', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateTrackinginBatchesResponse()
            )
        );

        $expectedParams = $this->mockCreateTrackinginBatchesParams();

        $response = $this->client->addBatchTracking($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('tracker_identifiers');
    });

    it('can create single tracking for single transaction', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreateTrackinginBatchesResponse()
            )
        );

        $expectedParams = $this->mockCreateTrackinginBatchesParams();

        $response = $this->client->addTracking($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('tracker_identifiers');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Webhooks
// ─────────────────────────────────────────────────────────────────────────────

describe('Webhooks', function () {
    it('can list web hooks event types', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListWebHookEventsTypesResponse()
            )
        );

        $response = $this->client->listEventTypes();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('event_types');
    });

    it('can list web hooks events', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockWebHookEventsListResponse()
            )
        );

        $response = $this->client->listEvents();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('events');
    });

    it('can show details for a web hooks event', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetWebHookEventResponse()
            )
        );

        $response = $this->client->showEventDetails('8PT597110X687430LKGECATA');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can resend notification for a web hooks event', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockResendWebHookEventNotificationResponse()
            )
        );

        $expectedParams = ['12334456'];

        $response = $this->client->resendEventNotification('8PT597110X687430LKGECATA', $expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
    });

    it('can create a web hook', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
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

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('event_types');
    });

    it('can list web hooks', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListWebHookResponse()
            )
        );

        $response = $this->client->listWebHooks();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('webhooks');
    });

    it('can delete a web hook', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deleteWebHook('5GP028458E2496506');

        expect($response)->toBeEmpty();
    });

    it('can update a web hook', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockUpdateWebHookResponse()
            )
        );

        $expectedParams = $this->mockUpdateWebHookParams();

        $response = $this->client->updateWebHook('0EH40505U7160970P', $expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('event_types');
    });

    it('can show details for a web hook', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockGetWebHookResponse()
            )
        );

        $response = $this->client->showWebHookDetails('0EH40505U7160970P');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('event_types');
    });

    it('can list events for web hooks', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListWebHookEventsResponse()
            )
        );

        $response = $this->client->listWebHookEvents('0EH40505U7160970P');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('event_types');
    });

    it('can verify web hook signature', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockVerifyWebHookSignatureResponse()
            )
        );

        $expectedParams = $this->mockVerifyWebHookSignatureParams();

        $response = $this->client->verifyWebHook($expectedParams);

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('verification_status');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Payment Tokens
// ─────────────────────────────────────────────────────────────────────────────

describe('Payment Tokens', function () {
    it('can list payment methods source tokens', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mockListPaymentMethodsTokensResponse(),
                $container
            )
        );

        $response = $this->client->setCustomerSource('customer_4029352050')
            ->listPaymentSourceTokens();

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('payment_tokens');

        // Regression guard: a raw PHP bool interpolates as "1" or "", not "true"/"false".
        // PayPal requires the literal string "true" or "false" for total_required.
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        $url = (string) $request->getUri();
        expect($url)->toContain('total_required=true');
        expect($url)->not->toContain('total_required=1');
    });

    it('throws when listPaymentSourceTokens is called without a customer ID', function () {
        // Regression: accessing $this->customer_source['id'] without a guard caused an
        // "Undefined array key 'id'" error rather than a clear, actionable message.
        expect(fn () => $this->client->listPaymentSourceTokens())
            ->toThrow(RuntimeException::class, 'A customer ID must be set via setCustomerId() before listing payment tokens.');
    });

    it('listPaymentSourceTokens sends total_required=false when totals disabled', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $container = [];
        $this->client->setClient(
            $this->mock_http_client_capturing(
                $this->mockListPaymentMethodsTokensResponse(),
                $container
            )
        );

        $this->client->setCustomerSource('customer_4029352050')
            ->listPaymentSourceTokens(1, 10, false);

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        $url = (string) $request->getUri();
        expect($url)->toContain('total_required=false');
        expect($url)->not->toContain('total_required=1');
        expect($url)->not->toContain('total_required=&');
    });

    it('can show details for payment method source token', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockCreatePaymentMethodsTokenResponse()
            )
        );

        $response = $this->client->showPaymentSourceTokenDetails('8kk8451t');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
        expect($response)->toHaveKey('customer');
        expect($response)->toHaveKey('payment_source');
    });

    it('can delete a payment method source token', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(false)
        );

        $response = $this->client->deletePaymentSourceToken('8kk8451t');

        expect($response)->toBeEmpty();
    });

    it('can show details for payment setup token', function () {
        $this->client->setAccessToken([
            'access_token' => $this->access_token,
            'token_type' => 'Bearer',
        ]);

        $this->client->setClient(
            $this->mock_http_client(
                $this->mockListPaymentSetupTokenResponse()
            )
        );

        $response = $this->client->showPaymentSetupTokenDetails('5C991763VB2781612');

        expect($response)->not->toBeEmpty();
        expect($response)->toHaveKey('id');
        expect($response)->toHaveKey('customer');
        expect($response)->toHaveKey('payment_source');
    });
});
