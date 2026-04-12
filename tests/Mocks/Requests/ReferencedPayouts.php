<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait ReferencedPayouts
{
    private function mockCreateReferencedBatchPayoutParams(): array
    {
        return Utils::jsonDecode('{
  "referenced_payouts": [
    {
      "reference_id": "2KP03934U4415543C",
      "reference_type": "TRANSACTION_ID"
    },
    {
      "reference_id": "8TA4226978212399L",
      "reference_type": "TRANSACTION_ID"
    }
  ]
}', true);
    }

    private function mockCreateReferencedBatchPayoutItemParams(): array
    {
        return Utils::jsonDecode('{
  "reference_id": "CAPTURETXNID",
  "reference_type": "TRANSACTION_ID"
}', true);
    }
}
