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
        return $this->sendDisputeDocuments(
            "v1/customer/disputes/{$dispute_id}/provide-evidence",
            $files,
            $evidences !== [] ? ['evidences' => array_values($evidences)] : null
        );
    }

    /**
     * Appeal a dispute with new evidence.
     *
     * Only possible when the dispute details contain an `appeal` HATEOAS link.
     * Files and evidence details are sent like provideDisputeEvidence(); the
     * details go in a JSON part named "input" ({"evidences": [...]}), following
     * PayPal's published samples. Verify evidence metadata in the sandbox.
     *
     * @param list<string>                           $files     Paths to evidence documents (jpg, png, pdf).
     * @param array<array-key, array<string, mixed>> $evidences Evidence details, e.g.
     *                                                          [['evidence_type' => 'PROOF_OF_FULFILLMENT', 'notes' => '...']].
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_appeal
     */
    public function appealDispute(string $dispute_id, array $files = [], array $evidences = [])
    {
        return $this->sendDisputeDocuments(
            "v1/customer/disputes/{$dispute_id}/appeal",
            $files,
            $evidences !== [] ? ['evidences' => array_values($evidences)] : null
        );
    }

    /**
     * Provide supporting information for a dispute.
     *
     * Only allowed in the CHARGEBACK, PRE_ARBITRATION and ARBITRATION life
     * cycle stages, when the dispute contains a `provide-supporting-info`
     * HATEOAS link. The notes are sent in a JSON part named "input"
     * ({"notes": "..."}), following PayPal's published samples; verify in the
     * sandbox.
     *
     * @param list<string> $files Optional supporting documents (jpg, png, pdf).
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_provide-supporting-info
     */
    public function provideDisputeSupportingInfo(string $dispute_id, string $notes, array $files = [])
    {
        return $this->sendDisputeDocuments(
            "v1/customer/disputes/{$dispute_id}/provide-supporting-info",
            $files,
            ['notes' => $notes]
        );
    }

    /**
     * Send a multipart dispute request: an optional JSON "input" part followed
     * by one part per file (named after the file).
     *
     * @param list<string>              $files
     * @param array<string, mixed>|null $input
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     */
    private function sendDisputeDocuments(string $endpoint, array $files, ?array $input)
    {
        if (VerifyDocuments::isValidEvidenceFile($files) === false) {
            $this->throwInvalidEvidenceFileException();
        }

        $this->apiEndPoint = $endpoint;

        $this->options['multipart'] = [];

        if ($input !== null) {
            $this->options['multipart'][] = [
                'name' => 'input',
                'contents' => json_encode($input, JSON_THROW_ON_ERROR),
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
     * Offer types ($refund_type):
     * - REFUND: refund $amount without return or replacement.
     * - REFUND_WITH_RETURN: refund $amount after the item is returned; pass
     *   'return_shipping_address' in $data.
     * - REFUND_WITH_REPLACEMENT: refund $amount and send a replacement.
     * - REPLACEMENT_WITHOUT_REFUND: send a replacement only; $amount is ignored
     *   and no offer_amount is sent.
     *
     * @param array<string, mixed> $data Additional fields merged into the request, e.g.
     *                                   'return_shipping_address' or 'invoice_id'.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/customer-disputes/v1/#disputes_make-offer
     */
    public function makeOfferToResolveDispute(string $dispute_id, string $dispute_note, float $amount, string $refund_type, array $data = [])
    {
        $this->apiEndPoint = "v1/customer/disputes/{$dispute_id}/make-offer";

        $data['note'] = $dispute_note;
        $data['offer_type'] = $refund_type;

        // PayPal requires offer_amount to be omitted for replacement-only offers.
        if ($refund_type !== 'REPLACEMENT_WITHOUT_REFUND') {
            $data['offer_amount'] = [
                'currency_code' => $this->getCurrency(),
                'value' => Amount::format($amount, $this->getCurrency()),
            ];
        }

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
     * 'REFUND'. Supported values: REFUND, REFUND_WITH_RETURN, PARTIAL_REFUND
     * (include `refund_amount`) and REFUND_WITH_RETURN_SHIPMENT_LABEL. Other
     * fields such as `return_shipping_address` can be passed in $data too.
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
