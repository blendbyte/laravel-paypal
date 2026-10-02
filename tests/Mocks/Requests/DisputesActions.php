<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;


trait DisputesActions
{
    protected function acceptDisputeClaimParams(): array
    {
        return json_decode('{
  "note": "Full refund to the customer.",
  "accept_claim_type": "REFUND"
}', true);
    }

    protected function acceptDisputeResolutionParams(): array
    {
        return json_decode('{
  "note": "I am ok with the refund offered."
}', true);
    }

    protected function acknowledgeItemReturnedParams(): array
    {
        return json_decode('{
  "note": "I have received the item back.",
  "acknowledgement_type": "ITEM_RECEIVED"
}', true);
    }

    protected function sendDisputeMessageParams(): array
    {
        return json_decode('{
  "message": "I have shipped the item. Tracking number: 1234567890."
}', true);
    }

    protected function makeOfferToResolveDisputeParams(): array
    {
        return [
            'note'         => 'Full refund to the customer.',
            'offer_type'   => 'REFUND',
            'offer_amount' => [
                'currency_code' => 'USD',
                'value'         => '10.00',
            ],
        ];
    }

    protected function escalateDisputeToClaimParams(): array
    {
        return ['note' => 'Escalating to a claim due to non-resolution.'];
    }

    protected function updateDisputeStatusParams(): array
    {
        return ['action' => 'SELLER_EVIDENCE'];
    }

    protected function settleDisputeParams(): array
    {
        return ['adjudication_outcome' => 'SELLER_FAVOR'];
    }

    protected function declineDisputeOfferResolutionParams(): array
    {
        return ['note' => 'I do not agree with the offer.'];
    }
}
