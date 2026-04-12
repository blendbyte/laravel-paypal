<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait ReferencedPayouts
{
    private function mockCreateReferencedBatchPayoutResponse(): array
    {
        return Utils::jsonDecode('{
  "links": [
    {
      "href": "https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts/CDZEC5MJ8R5HY",
      "rel": "self",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockShowReferencedBatchPayoutResponse(): array
    {
        return Utils::jsonDecode('{
  "referenced_payouts": [
    {
      "item_id": "dVeQhMc5Ck5WPw2gWYDLzh3qM2Dp1XbRlZb9fDouzLzDhx1eMYYTFe3syHEKKx4=",
      "processing_state": {
        "status": "SUCCESS"
      },
      "reference_id": "2KP03934U4415543C",
      "reference_type": "TRANSACTION_ID",
      "payout_amount": {
        "currency_code": "USD",
        "value": "1.0"
      },
      "payout_destination": "KJDHGANDJ4DPZ",
      "payout_transaction_id": "3KP03934U4415543D",
      "disbursement_transaction_id": "4KP03934U4415543E",
      "links": [
        {
          "href": "https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts-items/dVeQhMc5Ck5WPw2gWYDLzh3qM2Dp1XbRlZb9fDouzLzDhx1eMYYTFe3syHEKKx4=",
          "rel": "self",
          "method": "GET"
        }
      ]
    },
    {
      "item_id": "spK0ggOfijUiOUtbXBepp3h5tolruRWTl4aED-_6yz25POeNFABpkewSIxIXh4A=",
      "processing_state": {
        "status": "SUCCESS"
      },
      "reference_id": "8TA4226978212399L",
      "reference_type": "TRANSACTION_ID",
      "payout_amount": {
        "currency_code": "USD",
        "value": "1.0"
      },
      "payout_destination": "KJDHGANDJ4DPZ",
      "payout_transaction_id": "4KP03934U4415543D",
      "disbursement_transaction_id": "5KP03934U4415543E",
      "links": [
        {
          "href": "https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts-items/spK0ggOfijUiOUtbXBepp3h5tolruRWTl4aED-_6yz25POeNFABpkewSIxIXh4A=",
          "rel": "self",
          "method": "GET"
        }
      ]
    }
  ],
  "payout_directive": {
    "destination": "CC-CDZEC5MJ8R5HY",
    "type": "FINANCIAL_INSTRUMENT_ID"
  },
  "links": [
    {
      "href": "https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts/KHbwO28lWlXwi2IlToJ2IYNG4juFv6kpbFx4J9oQ5Hb24RSp96Dk5FudVHd6v4E=",
      "rel": "self",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockCreateReferencedBatchPayoutItemResponse(): array
    {
        return Utils::jsonDecode('{
  "item_id": "SOMEITEMID",
  "links": [
    {
      "href": "https://api-m.sandbox.paypal.com/v1/payments/referenced-payouts-items/SOMEITEMID",
      "rel": "self",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockShowReferencedBatchPayoutItemResponse(): array
    {
        return Utils::jsonDecode('{
  "item_id": "SOMEITEMID",
  "processing_state": {
    "status": "PROCESSING"
  },
  "reference_id": "CAPTURETXNID",
  "reference_type": "TRANSACTION_ID",
  "payout_amount": {
    "currency_code": "USD",
    "value": "2.0"
  },
  "payout_destination": "PAYERED",
  "payout_transaction_id": "PAYOUTTXNID",
  "links": [
    {
      "href": "https://api-m.paypal.com/v1/payments/referenced-payouts-items/SOMEITEMID",
      "rel": "self",
      "method": "GET"
    }
  ]
}', true);
    }
}
