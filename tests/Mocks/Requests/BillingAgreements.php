<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait BillingAgreements
{
    private function createBillingAgreementTokenParams(): array
    {
        return Utils::jsonDecode('{
  "payer": {
    "payment_method": "PAYPAL"
  },
  "plan": {
    "type": "MERCHANT_INITIATED_BILLING",
    "merchant_preferences": {
      "return_url": "https://example.com/return",
      "cancel_url": "https://example.com/cancel",
      "notify_url": "https://example.com/notify",
      "accepted_pymt_type": "INSTANT",
      "skip_shipping_address": false,
      "immutable_shipping_address": true
    }
  },
  "description": "Billing Agreement",
  "shipping_address": {
    "line1": "1350 North First Street",
    "city": "San Jose",
    "state": "CA",
    "postal_code": "95112",
    "country_code": "US",
    "recipient_name": "John Doe"
  }
}', true);
    }

    private function updateBillingAgreementParams(): array
    {
        return Utils::jsonDecode('[
  {
    "op": "replace",
    "path": "/",
    "value": {
      "description": "Example Billing Agreement",
      "merchant_custom_data": "INV-001"
    }
  },
  {
    "op": "replace",
    "path": "/plan/merchant_preferences/",
    "value": {
      "notify_url": "https://example.com/notify"
    }
  }
]', true);
    }
}
