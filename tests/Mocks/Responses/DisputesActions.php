<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait DisputesActions
{
    private function mockAcceptDisputesClaimResponse(): array
    {
        return Utils::jsonDecode('{
  "links": [
    {
      "rel": "self",
      "method": "GET",
      "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-D-27803"
    }
  ]
}', true);
    }

    private function mockAcceptDisputesOfferResolutionResponse(): array
    {
        return Utils::jsonDecode('{
  "links": [
    {
      "rel": "self",
      "method": "GET",
      "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-000-000-651-454"
    }
  ]
}', true);
    }

    private function mockAcknowledgeItemReturnedResponse(): array
    {
        return Utils::jsonDecode('{
  "links": [
    {
      "rel": "self",
      "method": "GET",
      "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-000-000-651-454"
    }
  ]
}', true);
    }

    private function mockSendDisputeMessageResponse(): array
    {
        return Utils::jsonDecode('{
  "links": [
    {
      "rel": "self",
      "method": "GET",
      "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-000-000-651-454"
    }
  ]
}', true);
    }
}
