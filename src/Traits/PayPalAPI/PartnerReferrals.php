<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI;

trait PartnerReferrals
{
    /**
     * Create a Partner Referral.
     *
     * @param array $partner_data
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/partner-referrals/v2/#partner-referrals_create
     */
    public function createPartnerReferral(array $partner_data)
    {
        $this->apiEndPoint = 'v2/customer/partner-referrals';

        $this->options['json'] = $partner_data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Get Partner Referral Details.
     *
     * @param string $partner_referral_id
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/partner-referrals/v2/#partner-referrals_read
     */
    public function showReferralData(string $partner_referral_id)
    {
        $this->apiEndPoint = "v2/customer/partner-referrals/{$partner_referral_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * List Seller Tracking Information.
     *
     * @param string $partner_id
     * @param string $tracking_id
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/partner-referrals/v1/#merchant-integration_find
     */
    public function listSellerTrackingInformation(string $partner_id, string $tracking_id)
    {
        $this->apiEndPoint = "v1/customer/partners/{$partner_id}/merchant-integrations?tracking_id={$tracking_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Show Seller Status.
     *
     * @param string $partner_id
     * @param string $merchant_id
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/partner-referrals/v1/#merchant-integration_status
     */
    public function showSellerStatus(string $partner_id, string $merchant_id)
    {
        $this->apiEndPoint = "v1/customer/partners/{$partner_id}/merchant-integrations/{$merchant_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * List Merchant Credentials.
     *
     * @param string $partner_id
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/partner-referrals/v1/#merchant-integration_credentials
     */
    public function listMerchantCredentials(string $partner_id)
    {
        $this->apiEndPoint = "v1/customer/partners/{$partner_id}/merchant-integrations/credentials";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
