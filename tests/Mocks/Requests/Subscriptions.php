<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;


trait Subscriptions
{
    private function mockCreateSubscriptionParams(): array
    {
        return json_decode('{
  "plan_id": "P-5ML4271244454362WXNWU5NQ",
  "start_time": "2018-11-01T00:00:00Z",
  "quantity": "20",
  "shipping_amount": {
    "currency_code": "USD",
    "value": "10.00"
  },
  "subscriber": {
    "name": {
      "given_name": "John",
      "surname": "Doe"
    },
    "email_address": "customer@example.com",
    "shipping_address": {
      "name": {
        "full_name": "John Doe"
      },
      "address": {
        "address_line_1": "2211 N First Street",
        "address_line_2": "Building 17",
        "admin_area_2": "San Jose",
        "admin_area_1": "CA",
        "postal_code": "95131",
        "country_code": "US"
      }
    }
  },
  "application_context": {
    "brand_name": "walmart",
    "locale": "en-US",
    "shipping_preference": "SET_PROVIDED_ADDRESS",
    "user_action": "SUBSCRIBE_NOW",
    "payment_method": {
      "payer_selected": "PAYPAL",
      "payee_preferred": "IMMEDIATE_PAYMENT_REQUIRED"
    },
    "return_url": "https://example.com/returnUrl",
    "cancel_url": "https://example.com/cancelUrl"
  }
}', true);
    }

    private function mockUpdateSubscriptionParams(): array
    {
        return json_decode('[
  {
    "op": "replace",
    "path": "/billing_info/outstanding_balance",
    "value": {
      "currency_code": "USD",
      "value": "50.00"
    }
  }
]', true);
    }

    /**
     * @return array
     */
    private function mockActivateSubscriptionParams()
    {
        return json_decode('{
  "reason": "Reactivating the subscription"
}', true);
    }

    /**
     * @return array
     */
    private function mockCancelSubscriptionParams()
    {
        return json_decode('{
  "reason": "Not satisfied with the service"
}', true);
    }

    /**
     * @return array
     */
    private function mockSuspendSubscriptionParams()
    {
        return json_decode('{
  "reason": "Item out of stock"
}', true);
    }

    /**
     * @return array
     */
    private function mockCaptureSubscriptionPaymentParams()
    {
        return json_decode('{
  "note": "Charging as the balance reached the limit",
  "capture_type": "OUTSTANDING_BALANCE",
  "amount": {
    "currency_code": "USD",
    "value": "100"
  }
}', true);
    }

    /**
     * @return array
     */
    private function mockUpdateSubscriptionItemsParams()
    {
        return json_decode('{
  "plan_id": "P-5ML4271244454362WXNWU5NQ",
  "shipping_amount": {
    "currency_code": "USD",
    "value": "10.00"
  },
  "shipping_address": {
    "name": {
      "full_name": "John Doe"
    },
    "address": {
      "address_line_1": "2211 N First Street",
      "address_line_2": "Building 17",
      "admin_area_2": "San Jose",
      "admin_area_1": "CA",
      "postal_code": "95131",
      "country_code": "US"
    }
  },
  "application_context": {
    "brand_name": "walmart",
    "locale": "en-US",
    "shipping_preference": "SET_PROVIDED_ADDRESS",
    "payment_method": {
      "payer_selected": "PAYPAL",
      "payee_preferred": "IMMEDIATE_PAYMENT_REQUIRED"
    },
    "return_url": "https://example.com/returnUrl",
    "cancel_url": "https://example.com/cancelUrl"
  }
}', true);
    }
}
