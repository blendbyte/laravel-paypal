<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait WebHooks
{
    private function mockCreateWebHookParams(): array
    {
        return Utils::jsonDecode('{
  "url": "https://example.com/example_webhook",
  "event_types": [
    {
      "name": "PAYMENT.AUTHORIZATION.CREATED"
    },
    {
      "name": "PAYMENT.AUTHORIZATION.VOIDED"
    }
  ]
}', true);
    }

    private function mockUpdateWebHookParams(): array
    {
        return Utils::jsonDecode('[
  {
    "op": "replace",
    "path": "/url",
    "value": "https://example.com/example_webhook_2"
  },
  {
    "op": "replace",
    "path": "/event_types",
    "value": [
      {
        "name": "PAYMENT.SALE.REFUNDED"
      }
    ]
  }
]', true);
    }

    private function mockResendWebHookEventNotificationParams(): array
    {
        return Utils::jsonDecode('{
  "webhook_ids": [
    "12334456"
  ]
}', true);
    }

    private function mockVerifyWebHookSignatureParams(): array
    {
        return Utils::jsonDecode('{
  "transmission_id": "69cd13f0-d67a-11e5-baa3-778b53f4ae55",
  "transmission_time": "2016-02-18T20:01:35Z",
  "cert_url": "cert_url",
  "auth_algo": "SHA256withRSA",
  "transmission_sig": "lmI95Jx3Y9nhR5SJWlHVIWpg4AgFk7n9bCHSRxbrd8A9zrhdu2rMyFrmz+Zjh3s3boXB07VXCXUZy/UFzUlnGJn0wDugt7FlSvdKeIJenLRemUxYCPVoEZzg9VFNqOa48gMkvF+XTpxBeUx/kWy6B5cp7GkT2+pOowfRK7OaynuxUoKW3JcMWw272VKjLTtTAShncla7tGF+55rxyt2KNZIIqxNMJ48RDZheGU5w1npu9dZHnPgTXB9iomeVRoD8O/jhRpnKsGrDschyNdkeh81BJJMH4Ctc6lnCCquoP/GzCzz33MMsNdid7vL/NIWaCsekQpW26FpWPi/tfj8nLA==",
  "webhook_id": "1JE4291016473214C",
  "webhook_event": {
    "id": "8PT597110X687430LKGECATA",
    "create_time": "2013-06-25T21:41:28Z",
    "resource_type": "authorization",
    "event_type": "PAYMENT.AUTHORIZATION.CREATED",
    "summary": "A payment authorization was created",
    "resource": {
      "id": "2DC87612EK520411B",
      "create_time": "2013-06-25T21:39:15Z",
      "update_time": "2013-06-25T21:39:17Z",
      "state": "authorized",
      "amount": {
        "total": "7.47",
        "currency": "USD",
        "details": {
          "subtotal": "7.47"
        }
      },
      "parent_payment": "PAY-36246664YD343335CKHFA4AY",
      "valid_until": "2013-07-24T21:39:15Z",
      "links": [
        {
          "href": "https://api.paypal.com/v1/payments/authorization/2DC87612EK520411B",
          "rel": "self",
          "method": "GET"
        },
        {
          "href": "https://api.paypal.com/v1/payments/authorization/2DC87612EK520411B/capture",
          "rel": "capture",
          "method": "POST"
        },
        {
          "href": "https://api.paypal.com/v1/payments/authorization/2DC87612EK520411B/void",
          "rel": "void",
          "method": "POST"
        },
        {
          "href": "https://api.paypal.com/v1/payments/payment/PAY-36246664YD343335CKHFA4AY",
          "rel": "parent_payment",
          "method": "GET"
        }
      ]
    }
  }
}', true);
    }
}
