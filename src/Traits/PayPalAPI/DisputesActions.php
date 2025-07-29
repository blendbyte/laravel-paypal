<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use GuzzleHttp\Psr7;
use Srmklive\PayPal\Services\VerifyDocuments;

trait DisputesActions
{
    /**
     * Acknowledge item has been returned.
     *
     * @param string $dispute_id
     * @param string $dispute_note
     * @param string $acknowledgement_type
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes-actions_acknowledge-return-item
     */
    public function acknowledgeItemReturned(string $dispute_id, string $dispute_note, string $acknowledgement_type)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/acknowledge-return-item";

        $this->options['json'] = [
            'note'                  => $dispute_note,
            'acknowledgement_type'  => $acknowledgement_type,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Providence evidence in support of a dispute.
     *
     * @param string $dispute_id
     * @param array  $files
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_provide-evidence
     */
    public function provideDisputeEvidence(string $dispute_id, array $files)
    {
        if (VerifyDocuments::isValidEvidenceFile($files) === false) {
            $this->throwInvalidEvidenceFileException();
        }

        $this->apiEndPoint = "/v1/customer/disputes/{$dispute_id}/provide-evidence";

        $this->setRequestHeader('Content-Type', 'multipart/form-data');

        $this->options['multipart'] = [];

        foreach ($files as $file) {
            $this->options['multipart'][] = [
                'name'     => basename($file),
                'contents' => Psr7\Utils::tryFopen($file, 'r'),
            ];
        }

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Make offer to resolve dispute claim.
     *
     * @param string $dispute_id
     * @param string $dispute_note
     * @param float  $amount
     * @param string $refund_type
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_make-offer
     */
    public function makeOfferToResolveDispute(string $dispute_id, string $dispute_note, float $amount, string $refund_type)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/make-offer";

        $data['note'] = $dispute_note;
        $data['offer_type'] = $refund_type;
        $data['offer_amount'] = [
            'currency_code' => $this->getCurrency(),
            'value'         => $amount,
        ];

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Escalate dispute to claim.
     *
     * @param string $dispute_id
     * @param string $dispute_note
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_escalate
     */
    public function escalateDisputeToClaim(string $dispute_id, string $dispute_note)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/escalate";

        $data['note'] = $dispute_note;

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Accept offer to resolve dispute.
     *
     * @param string $dispute_id
     * @param string $dispute_note
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_accept-offer
     */
    public function acceptDisputeOfferResolution(string $dispute_id, string $dispute_note)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/accept-offer";

        $this->options['json'] = [
            'note'  => $dispute_note,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Accept customer dispute claim.
     *
     * @param string $dispute_id
     * @param string $dispute_note
     * @param array  $data
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_accept-claim
     */
    public function acceptDisputeClaim(string $dispute_id, string $dispute_note, array $data = [])
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/accept-claim";

        $data['note'] = $dispute_note;
        $data['accept_claim_type'] = 'REFUND';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Update dispute status.
     *
     * @param string $dispute_id
     * @param bool   $merchant
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_require-evidence
     */
    public function updateDisputeStatus(string $dispute_id, bool $merchant = true)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/require-evidence";

        $data['action'] = ($merchant === true) ? 'SELLER_EVIDENCE' : 'BUYER_EVIDENCE';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Settle dispute.
     *
     * @param string $dispute_id
     * @param bool   $merchant
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_adjudicate
     */
    public function settleDispute(string $dispute_id, bool $merchant = true)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/adjudicate";

        $data['adjudication_outcome'] = ($merchant === true) ? 'SELLER_FAVOR' : 'BUYER_FAVOR';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Decline offer to resolve dispute.
     *
     * @param string $dispute_id
     * @param string $dispute_note
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_deny-offer
     */
    public function declineDisputeOfferResolution(string $dispute_id, string $dispute_note)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/deny-offer";

        $this->options['json'] = [
            'note'  => $dispute_note,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
