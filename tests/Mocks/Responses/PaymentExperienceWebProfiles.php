<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait PaymentExperienceWebProfiles
{
    private function mockListWebProfilesResponse(): array
    {
        return Utils::jsonDecode('[
  {
    "id": "XP-GCUV-X35G-HNEY-5MJY",
    "name": "exampleProfile",
    "flow_config": {
      "landing_page_type": "billing",
      "bank_txn_pending_url": "https://example.com/flow_config/"
    },
    "input_fields": {
      "no_shipping": 1,
      "address_override": 1
    },
    "presentation": {
      "logo_image": "https://example.com/logo_image/"
    }
  },
  {
    "id": "XP-A88A-LYLW-8Y3X-E5ER",
    "name": "exampleProfile",
    "flow_config": {
      "landing_page_type": "billing",
      "bank_txn_pending_url": "https://example.com/flow_config/"
    },
    "input_fields": {
      "no_shipping": 1,
      "address_override": 1
    },
    "presentation": {
      "logo_image": "https://example.com/logo_image/"
    }
  },
  {
    "id": "XP-RFV4-PVD8-AGHJ-8E5J",
    "name": "exampleProfile",
    "flow_config": {
      "bank_txn_pending_url": "https://example.com/flow_config/"
    },
    "input_fields": {
      "no_shipping": 1,
      "address_override": 1
    },
    "presentation": {
      "logo_image": "https://example.com/logo_image/"
    }
  }
]', true);
    }

    private function mockWebProfileResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "XP-RFV4-PVD8-AGHJ-8E5J",
  "name": "exampleProfile",
  "temporary": false,
  "presentation": {
    "logo_image": "https://example.com/logo_image/"
  },
  "input_fields": {
    "no_shipping": 1,
    "address_override": 1
  },
  "flow_config": {
    "landing_page_type": "billing",
    "bank_txn_pending_url": "https://example.com/flow_config/",
    "user_action": "commit"
  }
}', true);
    }
}
