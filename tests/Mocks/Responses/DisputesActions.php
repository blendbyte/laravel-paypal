<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;


trait DisputesActions
{
    private function mockAcceptDisputesClaimResponse(): array
    {
        return json_decode('{
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
        return json_decode('{
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
        return json_decode('{
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
        return json_decode('{
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
