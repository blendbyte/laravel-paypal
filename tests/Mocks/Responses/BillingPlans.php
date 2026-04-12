<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait BillingPlans
{
    private function mockCreatePlansResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "P-5ML4271244454362WXNWU5NQ",
  "product_id": "PROD-XXCD1234QWER65782",
  "name": "Video Streaming Service Plan",
  "description": "Video Streaming Service basic plan",
  "status": "ACTIVE",
  "billing_cycles": [
    {
      "frequency": {
        "interval_unit": "MONTH",
        "interval_count": 1
      },
      "tenure_type": "TRIAL",
      "sequence": 1,
      "total_cycles": 2,
      "pricing_scheme": {
        "fixed_price": {
          "value": "3",
          "currency_code": "USD"
        },
        "version": 1,
        "create_time": "2020-05-27T12:13:51Z",
        "update_time": "2020-05-27T12:13:51Z"
      }
    },
    {
      "frequency": {
        "interval_unit": "MONTH",
        "interval_count": 1
      },
      "tenure_type": "TRIAL",
      "sequence": 2,
      "total_cycles": 3,
      "pricing_scheme": {
        "fixed_price": {
          "currency_code": "USD",
          "value": "6"
        },
        "version": 1,
        "create_time": "2020-05-27T12:13:51Z",
        "update_time": "2020-05-27T12:13:51Z"
      }
    },
    {
      "frequency": {
        "interval_unit": "MONTH",
        "interval_count": 1
      },
      "tenure_type": "REGULAR",
      "sequence": 3,
      "total_cycles": 12,
      "pricing_scheme": {
        "fixed_price": {
          "currency_code": "USD",
          "value": "10"
        },
        "version": 1,
        "create_time": "2020-05-27T12:13:51Z",
        "update_time": "2020-05-27T12:13:51Z"
      }
    }
  ],
  "payment_preferences": {
    "auto_bill_outstanding": true,
    "setup_fee": {
      "value": "10",
      "currency_code": "USD"
    },
    "setup_fee_failure_action": "CONTINUE",
    "payment_failure_threshold": 3
  },
  "taxes": {
    "percentage": "10",
    "inclusive": false
  },
  "create_time": "2020-05-27T12:13:51Z",
  "update_time": "2020-05-27T12:13:51Z",
  "links": [
    {
      "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ",
      "rel": "edit",
      "method": "PATCH"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ/deactivate",
      "rel": "deactivate",
      "method": "POST"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ/update-pricing-schemes",
      "rel": "edit",
      "method": "POST"
    }
  ]
}', true);
    }

    private function mockListPlansResponse(): array
    {
        return Utils::jsonDecode('{
  "total_items": 12,
  "total_pages": 6,
  "plans": [
    {
      "id": "P-5ML4271244454362WXNWU5NQ",
      "product_id": "PROD-XXCD1234QWER65782",
      "status": "ACTIVE",
      "name": "Zoho Marketing Campaign  Plan",
      "description": "Zoho Marketing Campaign Plan",
      "create_time": "2018-12-10T21:20:49Z",
      "links": [
        {
          "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ",
          "rel": "self",
          "method": "GET"
        }
      ]
    },
    {
      "id": "P-6LL4271454454362WXNWU5NQ",
      "product_id": "PROD-XXCD1234QWER65782",
      "status": "ACTIVE",
      "name": "Zoho Marketing Campaign Basic Plan",
      "description": "Zoho Marketing Campaign Plan",
      "create_time": "2019-01-10T21:20:49Z",
      "links": [
        {
          "href": "https://api.paypal.com/v1/billing/plans/P-6LL4271454454362WXNWU5NQ",
          "rel": "self",
          "method": "GET"
        }
      ]
    }
  ],
  "links": [
    {
      "href": "https://api.paypal.com/v1/billing/plans?product_id=PROD-XXCD1234QWER65782&page_size=2&page=1",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans?product_id=PROD-XXCD1234QWER65782&page_size=2&page=1",
      "rel": "first",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans?product_id=PROD-XXCD1234QWER65782&page_size=2&page=2",
      "rel": "next",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans?product_id=PROD-XXCD1234QWER65782&page_size=2&page=6",
      "rel": "last",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockGetPlansResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "P-5ML4271244454362WXNWU5NQ",
  "product_id": "PROD-XXCD1234QWER65782",
  "name": "Basic Plan",
  "description": "Basic Plan",
  "status": "ACTIVE",
  "billing_cycles": [
    {
      "frequency": {
        "interval_unit": "MONTH",
        "interval_count": 1
      },
      "tenure_type": "TRIAL",
      "sequence": 1,
      "total_cycles": 2,
      "pricing_scheme": {
        "fixed_price": {
          "currency_code": "USD",
          "value": "3"
        },
        "version": 1,
        "create_time": "2020-05-27T12:13:51Z",
        "update_time": "2020-05-27T12:13:51Z"
      }
    },
    {
      "frequency": {
        "interval_unit": "MONTH",
        "interval_count": 1
      },
      "tenure_type": "TRIAL",
      "sequence": 2,
      "total_cycles": 3,
      "pricing_scheme": {
        "fixed_price": {
          "currency_code": "USD",
          "value": "6"
        },
        "version": 1,
        "create_time": "2020-05-27T12:13:51Z",
        "update_time": "2020-05-27T12:13:51Z"
      }
    },
    {
      "frequency": {
        "interval_unit": "MONTH",
        "interval_count": 1
      },
      "tenure_type": "REGULAR",
      "sequence": 3,
      "total_cycles": 12,
      "pricing_scheme": {
        "fixed_price": {
          "value": "10",
          "currency_code": "USD"
        },
        "status": "ACTIVE",
        "version": 1,
        "create_time": "2020-05-27T12:13:51Z",
        "update_time": "2020-05-27T12:13:51Z"
      }
    }
  ],
  "taxes": {
    "percentage": "10",
    "inclusive": false
  },
  "create_time": "2020-05-27T12:13:51Z",
  "update_time": "2020-05-27T12:13:51Z",
  "links": [
    {
      "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ",
      "rel": "edit",
      "method": "PATCH"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ/deactivate",
      "rel": "deactivate",
      "method": "POST"
    },
    {
      "href": "https://api.paypal.com/v1/billing/plans/P-5ML4271244454362WXNWU5NQ/update-pricing-schemes",
      "rel": "edit",
      "method": "POST"
    }
  ]
}', true);
    }

    private function mockCreatePlansErrorResponse(): array
    {
        return Utils::jsonDecode('{
    "error": {
        "name" : "UNPROCESSABLE_ENTITY",
        "message" : "The requested action could not be performed, semantically incorrect, or failed business validation.",
        "debug_id" : "7a944631e76bf",
        "details" : [
            {
                "issue" : "CURRENCY_NOT_SUPPORTED_FOR_RECEIVER",
                "description" : "This currency cannot be accepted for this recipient\'s account."
            }
        ],
        "links" : [
            {
                "href" : "https://developer.paypal.com/docs/api/v1/billing/subscriptions#UNPROCESSABLE_ENTITY",
                "rel" : "information_link",
                "method" : "GET"
            }
        ]
    }
}', true);
    }
}
