<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait BillingAgreements
{
    private function mockCreateBillingAgreementTokenResponse(): array
    {
        return Utils::jsonDecode('{
  "links": [
    {
      "href": "https://api-m.sandbox.paypal.com/agreements/approve?ba_token=BA-8A802366G0648845Y",
      "rel": "approval_url",
      "method": "POST"
    },
    {
      "href": "https://api-m.sandbox.paypal.com/v1/billing-agreements/BA-8A802366G0648845Y/agreements",
      "rel": "self",
      "method": "POST"
    }
  ],
  "token_id": "BA-8A802366G0648845Y"
}', true);
    }

    private function mockGetBillingAgreementTokenResponse(): array
    {
        return Utils::jsonDecode('{
  "description": "Billing Agreement",
  "token_id": "BA-8A802366G0648845Y",
  "token_status": "PENDING",
  "skip_shipping_address": false,
  "immutable_shipping_address": true,
  "redirect_urls": {
    "cancel_url": "https://example.com/cancel",
    "return_url": "https://example.com/return",
    "notify_url": "https://example.com/notify"
  },
  "plan_unit_list": [
    {
      "id": "BA-8A802366G0648845Y",
      "billing_type": "MERCHANT_INITIATED_BILLING"
    }
  ],
  "owner": {
    "merchant_id": "J6LF2WT3H97J6",
    "email": "merchant@example.com"
  }
}', true);
    }

    private function mockCreateBillingAgreementResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "B-50V812176H0783741",
  "state": "ACTIVE",
  "description": "Billing Agreement",
  "payer": {
    "payer_info": {
      "email": "doe@example.com",
      "first_name": "John",
      "last_name": "Doe",
      "payer_id": "ZU7HZ76P4VL5U"
    }
  },
  "plan": {
    "type": "MERCHANT_INITIATED_BILLING",
    "merchant_preferences": {
      "notify_url": "https://example.com/notify",
      "accepted_pymt_type": "INSTANT"
    }
  },
  "links": [
    {
      "href": "https://api-m.sandbox.paypal.com/v1/billing-agreements/agreements/B-50V812176H0783741/cancel",
      "rel": "cancel",
      "method": "POST"
    },
    {
      "href": "https://api-m.sandbox.paypal.com/v1/billing-agreements/agreements/B-50V812176H0783741",
      "rel": "self",
      "method": "GET"
    }
  ],
  "merchant": {
    "payee_info": {
      "email": "merchant@example.com"
    }
  },
  "create_time": "2017-08-08T07:19:28.000Z",
  "update_time": "2017-08-08T07:19:28.000Z"
}', true);
    }

    private function mockShowBillingAgreementResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "B-50V812176H0783741",
  "state": "ACTIVE",
  "description": "Billing Agreement",
  "payer": {
    "payer_info": {
      "email": "doe@example.com",
      "first_name": "John",
      "last_name": "Doe",
      "payer_id": "ZU7HZ76P4VL5U"
    }
  },
  "plan": {
    "type": "MERCHANT_INITIATED_BILLING",
    "merchant_preferences": {
      "notify_url": "https://example.com/notify",
      "accepted_pymt_type": "INSTANT"
    }
  },
  "links": [
    {
      "href": "https://api-m.sandbox.paypal.com/v1/billing-agreements/agreements/B-50V812176H0783741/cancel",
      "rel": "cancel",
      "method": "POST"
    },
    {
      "href": "https://api-m.sandbox.paypal.com/v1/billing-agreements/agreements/B-50V812176H0783741",
      "rel": "self",
      "method": "GET"
    }
  ],
  "merchant": {
    "payee_info": {
      "email": "merchant@example.com"
    }
  },
  "create_time": "2017-08-08T07:19:28.000Z",
  "update_time": "2017-08-08T07:19:28.000Z"
}', true);
    }
}
