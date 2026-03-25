<?php

namespace Blendbyte\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait DisputesActions
{
    protected function acceptDisputeClaimParams(): array
    {
        return Utils::jsonDecode('{
  "note": "Full refund to the customer.",
  "accept_claim_type": "REFUND"
}', true);
    }

    protected function acceptDisputeResolutionParams(): array
    {
        return Utils::jsonDecode('{
  "note": "I am ok with the refund offered."
}', true);
    }

    protected function acknowledgeItemReturnedParams(): array
    {
        return Utils::jsonDecode('{
  "note": "I have received the item back.",
  "acknowledgement_type": "ITEM_RECEIVED"
}', true);
    }
}
