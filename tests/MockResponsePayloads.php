<?php

namespace Srmklive\PayPal\Tests;

trait MockResponsePayloads
{
    use Mocks\Responses\BillingAgreements;
    use Mocks\Responses\BillingPlans;
    use Mocks\Responses\CatalogProducts;
    use Mocks\Responses\Disputes;
    use Mocks\Responses\DisputesActions;
    use Mocks\Responses\Identity;
    use Mocks\Responses\Invoices;
    use Mocks\Responses\InvoicesSearch;
    use Mocks\Responses\InvoicesTemplates;
    use Mocks\Responses\Orders;
    use Mocks\Responses\PartnerReferrals;
    use Mocks\Responses\PaymentAuthorizations;
    use Mocks\Responses\PaymentCaptures;
    use Mocks\Responses\PaymentExperienceWebProfiles;
    use Mocks\Responses\PaymentMethodsTokens;
    use Mocks\Responses\PaymentRefunds;
    use Mocks\Responses\Payouts;
    use Mocks\Responses\ReferencedPayouts;
    use Mocks\Responses\Reporting;
    use Mocks\Responses\Subscriptions;
    use Mocks\Responses\Trackers;
    use Mocks\Responses\WebHooks;

    private function mockAccessTokenResponse(): array
    {
        return [
            'scope' => 'some_scope',
            'access_token' => 'some_access_token',
            'token_type' => 'Bearer',
            'app_id' => 'APP-80W284485P519543T',
            'expires_in' => 32400,
            'nonce' => 'some_nonce',
        ];
    }
}
