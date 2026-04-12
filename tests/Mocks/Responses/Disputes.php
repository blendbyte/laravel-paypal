<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait Disputes
{
    private function mockListDisputesResponse(): array
    {
        return Utils::jsonDecode('{
  "items": [
    {
      "dispute_id": "PP-000-003-648-191",
      "create_time": "2017-01-24T10:41:35.000Z",
      "update_time": "2017-01-24T11:40:32.000Z",
      "status": "WAITING_FOR_SELLER_RESPONSE",
      "reason": "MERCHANDISE_OR_SERVICE_NOT_RECEIVED",
      "dispute_state": "REQUIRED_OTHER_PARTY_ACTION",
      "dispute_amount": {
        "currency_code": "USD",
        "value": "50.00"
      },
      "links": [
        {
          "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-000-003-648-191",
          "rel": "self",
          "method": "GET"
        }
      ]
    },
    {
      "dispute_id": "PP-000-003-648-175",
      "create_time": "2017-01-24T10:37:23.000Z",
      "update_time": "2017-01-24T11:32:32.000Z",
      "status": "UNDER_REVIEW",
      "reason": "UNAUTHORISED",
      "dispute_amount": {
        "currency_code": "USD",
        "value": "20.00"
      },
      "links": [
        {
          "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-000-003-648-175",
          "rel": "self",
          "method": "GET"
        }
      ]
    }
  ],
  "links": [
    {
      "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes",
      "rel": "first",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockGetDisputesResponse(): array
    {
        return Utils::jsonDecode('{
  "dispute_id": "PP-D-4012",
  "create_time": "2019-04-11T04:18:00.000Z",
  "update_time": "2019-04-21T04:19:08.000Z",
  "disputed_transactions": [
    {
      "seller_transaction_id": "3BC38643YC807283D",
      "create_time": "2019-04-11T04:16:58.000Z",
      "transaction_status": "REVERSED",
      "gross_amount": {
        "currency_code": "USD",
        "value": "192.00"
      },
      "buyer": {
        "name": "Lupe Justin"
      },
      "seller": {
        "email": "merchant@example.com",
        "merchant_id": "5U29WL78XSAEL",
        "name": "Lesley Paul"
      }
    }
  ],
  "reason": "MERCHANDISE_OR_SERVICE_NOT_AS_DESCRIBED",
  "status": "RESOLVED",
  "dispute_amount": {
    "currency_code": "USD",
    "value": "96.00"
  },
  "dispute_outcome": {
    "outcome_code": "RESOLVED_BUYER_FAVOUR",
    "amount_refunded": {
      "currency_code": "USD",
      "value": "96.00"
    }
  },
  "dispute_life_cycle_stage": "CHARGEBACK",
  "dispute_channel": "INTERNAL",
  "messages": [
    {
      "posted_by": "BUYER",
      "time_posted": "2019-04-11T04:18:04.000Z",
      "content": "SNAD case created through automation"
    }
  ],
  "extensions": {
    "merchandize_dispute_properties": {
      "issue_type": "SERVICE",
      "service_details": {
        "sub_reasons": [
          "INCOMPLETE"
        ],
        "purchase_url": "https://ebay.in"
      }
    }
  },
  "offer": {
    "buyer_requested_amount": {
      "currency_code": "USD",
      "value": "96.00"
    }
  },
  "links": [
    {
      "href": "https://api-m.sandbox.paypal.com/v1/customer/disputes/PP-D-4012",
      "rel": "self",
      "method": "GET"
    }
  ]
}', true);
    }
}
