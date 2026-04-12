<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait PaymentAuthorizations
{
    private function mockGetAuthorizedPaymentDetailsResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "0VF52814937998046",
  "status": "AUTHORIZED",
  "amount": {
    "total": "10.99",
    "currency": "USD"
  },
  "invoice_id": "INVOICE-123",
  "seller_protection": {
    "status": "ELIGIBLE",
    "dispute_categories": [
      "ITEM_NOT_RECEIVED",
      "UNAUTHORIZED_TRANSACTION"
    ]
  },
  "expiration_time": "2017-10-10T23:23:45Z",
  "create_time": "2017-09-11T23:23:45Z",
  "update_time": "2017-09-11T23:23:45Z",
  "links": [
    {
      "rel": "self",
      "method": "GET",
      "href": "https://api.paypal.com/v2/payments/authorizations/0VF52814937998046"
    },
    {
      "rel": "capture",
      "method": "POST",
      "href": "https://api.paypal.com/v2/payments/authorizations/0VF52814937998046/capture"
    },
    {
      "rel": "void",
      "method": "POST",
      "href": "https://api.paypal.com/v2/payments/authorizations/0VF52814937998046/void"
    },
    {
      "rel": "reauthorize",
      "method": "POST",
      "href": "https://api.paypal.com/v2/payments/authorizations/0VF52814937998046/reauthorize"
    }
  ]
}', true);
    }

    private function mockCaptureAuthorizedPaymentResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "2GG279541U471931P",
  "status": "COMPLETED",
  "links": [
    {
      "rel": "self",
      "method": "GET",
      "href": "https://api.paypal.com/v2/payments/captures/2GG279541U471931P"
    },
    {
      "rel": "refund",
      "method": "POST",
      "href": "https://api.paypal.com/v2/payments/captures/2GG279541U471931P/refund"
    },
    {
      "rel": "up",
      "method": "GET",
      "href": "https://api.paypal.com/v2/payments/authorizations/0VF52814937998046"
    }
  ]
}', true);
    }

    private function mockReAuthorizeAuthorizedPaymentResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "8AA831015G517922L",
  "status": "CREATED",
  "links": [
    {
      "rel": "self",
      "method": "GET",
      "href": "https://api.paypal.com/v2/payments/authorizations/8AA831015G517922L"
    },
    {
      "rel": "capture",
      "method": "POST",
      "href": "https://api.paypal.com/v2/payments/authorizations/8AA831015G517922L/capture"
    },
    {
      "rel": "void",
      "method": "POST",
      "href": "https://api.paypal.com/v2/payments/authorizations/8AA831015G517922L/void"
    },
    {
      "rel": "reauthorize",
      "method": "POST",
      "href": "https://api.paypal.com/v2/payments/authorizations/8AA831015G517922L/reauthorize"
    }
  ]
}', true);
    }
}
