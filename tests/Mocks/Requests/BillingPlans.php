<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait BillingPlans
{
    private function createPlanParams(): array
    {
        return Utils::jsonDecode('{
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
        }
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
          "value": "6",
          "currency_code": "USD"
        }
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
        }
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
  }
}', true);
    }

    private function updatePlanParams(): array
    {
        return Utils::jsonDecode('[
  {
    "op": "replace",
    "path": "/payment_preferences/payment_failure_threshold",
    "value": 7
  }
]', true);
    }

    private function updatePlanPricingParams(): array
    {
        return Utils::jsonDecode('{
  "pricing_schemes": [
    {
      "billing_cycle_sequence": 2,
      "pricing_scheme": {
        "fixed_price": {
          "value": "50",
          "currency_code": "USD"
        }
      }
    }
  ]
}', true);
    }
}
