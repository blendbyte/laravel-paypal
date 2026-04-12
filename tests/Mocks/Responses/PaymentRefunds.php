<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait PaymentRefunds
{
    private function mockGetRefundDetailsResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "1JU08902781691411",
  "amount": {
    "value": "10.99",
    "currency_code": "USD"
  },
  "status": "COMPLETED",
  "note_to_payer": "Defective product",
  "seller_payable_breakdown": {
    "gross_amount": {
      "value": "10.99",
      "currency_code": "USD"
    },
    "paypal_fee": {
      "value": "0",
      "currency_code": "USD"
    },
    "net_amount": {
      "value": "10.99",
      "currency_code": "USD"
    },
    "total_refunded_amount": {
      "value": "10.99",
      "currency_code": "USD"
    }
  },
  "invoice_id": "INVOICE-123",
  "create_time": "2018-09-11T23:24:19Z",
  "update_time": "2018-09-11T23:24:19Z",
  "links": [
    {
      "rel": "self",
      "method": "GET",
      "href": "https://api.paypal.com/v2/payments/refunds/1JU08902781691411"
    },
    {
      "rel": "up",
      "method": "GET",
      "href": "https://api.paypal.com/v2/payments/captures/2GG279541U471931P"
    }
  ]
}', true);
    }
}
