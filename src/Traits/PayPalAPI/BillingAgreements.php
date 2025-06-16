<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

/**
 * This trait provides methods for the Reference Transactions API,
 * which is available on a limited-use basis.
 *
 * See: https://developer.paypal.com/limited-release/reference-transactions/
 * and https://developer.paypal.com/api/limited-release/reference-transactions/v1/
 * for more details.
 */
trait BillingAgreements
{
    /**
     * Create a new billing agreement.
     *
     * @param array $data
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/api/limited-release/reference-transactions/v1/#agreement-tokens_post
     */
    public function createBillingAgreementToken(array $data)
    {
        $this->apiEndPoint = '/v1/billing-agreements/agreement-tokens';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Get details of a billing agreement token.
     *
     * @param string $token_id
     *
     * @throws \Throwable
     *
     * @return array|string|\Psr\Http\Message\StreamInterface
     *
     * @see https://developer.paypal.com/api/limited-release/reference-transactions/v1/#agreement-tokens_get
     */
    public function getBillingAgreementTokenDetails(string $token_id)
    {
        $this->apiEndPoint = "/v1/billing-agreements/agreement-tokens/{$token_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Create a billing agreement.
     *
     * @param string $token_id
     *
     * @throws \Throwable
     *
     * @return array|string|\Psr\Http\Message\StreamInterface
     *
     * @see https://developer.paypal.com/api/limited-release/reference-transactions/v1/#agreements_create
     */
    public function createBillingAgreement(string $token_id)
    {
        $this->apiEndPoint = '/v1/billing-agreements/agreements';

        $this->options['json'] = [
            'token_id' => $token_id,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Update an existing billing agreement.
     *
     * @param string $agreement_id
     * @param array  $data
     *
     * @throws \Throwable
     *
     * @return array|string|\Psr\Http\Message\StreamInterface
     *
     * @see https://developer.paypal.com/api/limited-release/reference-transactions/v1/#agreements_patch
     */
    public function updateBillingAgreement(string $agreement_id, array $data)
    {
        $this->apiEndPoint = "/v1/billing-agreements/agreements/{$agreement_id}";

        $this->options['json'] = $data;

        $this->verb = 'patch';

        return $this->doPayPalRequest(false);
    }

    /**
     * Show details for an existing billing agreement.
     *
     * @param string $agreement_id
     *
     * @throws \Throwable
     *
     * @return array|string|\Psr\Http\Message\StreamInterface
     *
     * @see https://developer.paypal.com/api/limited-release/reference-transactions/v1/#agreements_get
     */
    public function showBillingAgreementDetails(string $agreement_id)
    {
        $this->apiEndPoint = "/v1/billing-agreements/agreements/{$agreement_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Cancel an existing billing agreement.
     *
     * @param string $agreement_id
     *
     * @throws \Throwable
     *
     * @return array|string|\Psr\Http\Message\StreamInterface
     *
     * @see https://developer.paypal.com/api/limited-release/reference-transactions/v1/#agreements_cancel
     */
    public function cancelBillingAgreement(string $agreement_id)
    {
        $this->apiEndPoint = "/v1/billing-agreements/agreements/{$agreement_id}/cancel";

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }
}
