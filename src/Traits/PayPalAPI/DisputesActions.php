<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Srmklive\PayPal\Services\Amount;
use Srmklive\PayPal\Services\VerifyDocuments;
use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;

trait DisputesActions
{
    /**
     * Acknowledge item has been returned.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes-actions_acknowledge-return-item
     */
    public function acknowledgeItemReturned(string $dispute_id, string $dispute_note, string $acknowledgement_type)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/acknowledge-return-item";

        $this->options['json'] = [
            'note' => $dispute_note,
            'acknowledgement_type' => $acknowledgement_type,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Provide evidence in support of a dispute.
     *
     * Files are uploaded as multipart parts. Optionally pass $evidences to
     * describe them (evidence_type, notes, evidence_info such as tracking
     * numbers or refund IDs); they are sent as a JSON part named "input"
     * containing {"evidences": [...]}, following PayPal's published request
     * samples. The part name is not specified in PayPal's OpenAPI spec, so
     * verify evidence metadata in the sandbox.
     *
     * @param list<string>                           $files     Paths to evidence documents (jpg, png, pdf).
     * @param array<array-key, array<string, mixed>> $evidences Evidence details, e.g.
     *                                                          [['evidence_type' => 'PROOF_OF_FULFILLMENT', 'notes' => '...']].
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_provide-evidence
     *
     * @throws \Throwable
     */
    public function provideDisputeEvidence(string $dispute_id, array $files, array $evidences = [])
    {
        if (VerifyDocuments::isValidEvidenceFile($files) === false) {
            $this->throwInvalidEvidenceFileException();
        }

        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/provide-evidence";

        $this->options['multipart'] = [];

        if ($evidences !== []) {
            $this->options['multipart'][] = [
                'name' => 'input',
                'contents' => json_encode(['evidences' => array_values($evidences)], JSON_THROW_ON_ERROR),
                'headers' => ['Content-Type' => 'application/json'],
            ];
        }

        foreach ($files as $file) {
            $this->options['multipart'][] = [
                'name' => basename($file),
                'contents' => Psr7\Utils::tryFopen($file, 'r'),
            ];
        }

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Make offer to resolve dispute claim.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
            'value' => Amount::format($amount, $this->getCurrency()),
        ];

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Escalate dispute to claim.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_accept-offer
     */
    public function acceptDisputeOfferResolution(string $dispute_id, string $dispute_note)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/accept-offer";

        $this->options['json'] = [
            'note' => $dispute_note,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Accept customer dispute claim.
     *
     * Pass `accept_claim_type` in $data to use a value other than the default
     * 'REFUND'. PayPal-supported values: REFUND, MERCHANDISE, MISSING_ITEM,
     * MISSING_REFUND, UNABLE_TO_COMPLETE_REFUND.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_accept-claim
     */
    public function acceptDisputeClaim(string $dispute_id, string $dispute_note, array $data = [])
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/accept-claim";

        $data['note'] = $dispute_note;
        $data['accept_claim_type'] ??= 'REFUND';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Update dispute status.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_deny-offer
     */
    public function declineDisputeOfferResolution(string $dispute_id, string $dispute_note)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/deny-offer";

        $this->options['json'] = [
            'note' => $dispute_note,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Send a message about a dispute to the other party.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_send-message
     */
    public function sendDisputeMessage(string $dispute_id, string $message)
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/send-message";

        $this->options['json'] = [
            'message' => $message,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
