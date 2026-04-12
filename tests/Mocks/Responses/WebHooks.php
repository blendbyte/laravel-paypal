<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait WebHooks
{
    private function mockCreateWebHookResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "0EH40505U7160970P",
  "url": "https://example.com/example_webhook",
  "event_types": [
    {
      "name": "PAYMENT.AUTHORIZATION.CREATED",
      "description": "A payment authorization was created."
    },
    {
      "name": "PAYMENT.AUTHORIZATION.VOIDED",
      "description": "A payment authorization was voided."
    }
  ],
  "links": [
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "update",
      "method": "PATCH"
    },
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "delete",
      "method": "DELETE"
    }
  ]
}', true);
    }

    private function mockListWebHookResponse(): array
    {
        return Utils::jsonDecode('{
  "webhooks": [
    {
      "id": "40Y916089Y8324740",
      "url": "https://example.com/example_webhook",
      "event_types": [
        {
          "name": "PAYMENT.AUTHORIZATION.CREATED",
          "description": "A payment authorization was created."
        },
        {
          "name": "PAYMENT.AUTHORIZATION.VOIDED",
          "description": "A payment authorization was voided."
        }
      ],
      "links": [
        {
          "href": "https://api.paypal.com/v1/notifications/webhooks/40Y916089Y8324740",
          "rel": "self",
          "method": "GET"
        },
        {
          "href": "https://api.paypal.com/v1/notifications/webhooks/40Y916089Y8324740",
          "rel": "update",
          "method": "PATCH"
        },
        {
          "href": "https://api.paypal.com/v1/notifications/webhooks/40Y916089Y8324740",
          "rel": "delete",
          "method": "DELETE"
        }
      ]
    },
    {
      "id": "0EH40505U7160970P",
      "url": "https://example.com/another_example_webhook",
      "event_types": [
        {
          "name": "PAYMENT.AUTHORIZATION.CREATED",
          "description": "A payment authorization was created."
        },
        {
          "name": "PAYMENT.AUTHORIZATION.VOIDED",
          "description": "A payment authorization was voided."
        }
      ],
      "links": [
        {
          "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
          "rel": "self",
          "method": "GET"
        },
        {
          "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
          "rel": "update",
          "method": "PATCH"
        },
        {
          "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
          "rel": "delete",
          "method": "DELETE"
        }
      ]
    }
  ]
}', true);
    }

    private function mockUpdateWebHookResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "0EH40505U7160970P",
  "url": "https://example.com/example_webhook_2",
  "event_types": [
    {
      "name": "PAYMENT.SALE.REFUNDED",
      "description": "A sale payment was refunded."
    }
  ],
  "links": [
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "update",
      "method": "PATCH"
    },
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "delete",
      "method": "DELETE"
    }
  ]
}', true);
    }

    private function mockGetWebHookResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "0EH40505U7160970P",
  "url": "https://example.com/example_webhook",
  "event_types": [
    {
      "name": "PAYMENT.AUTHORIZATION.CREATED",
      "description": "A payment authorization was created.",
      "status": "ENABLED"
    },
    {
      "name": "PAYMENT.AUTHORIZATION.VOIDED",
      "description": "A payment authorization was voided.",
      "status": "ENABLED"
    }
  ],
  "links": [
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "update",
      "method": "PATCH"
    },
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks/0EH40505U7160970P",
      "rel": "delete",
      "method": "DELETE"
    }
  ]
}', true);
    }

    private function mockListWebHookEventsResponse(): array
    {
        return Utils::jsonDecode('{
  "event_types": [
    {
      "name": "PAYMENT.AUTHORIZATION.CREATED",
      "description": "A payment authorization was created.",
      "status": "ENABLED"
    },
    {
      "name": "PAYMENT.AUTHORIZATION.VOIDED",
      "description": "A payment authorization was voided.",
      "status": "ENABLED"
    },
    {
      "name": "RISK.DISPUTE.CREATED",
      "description": "A dispute was filed against a transaction.",
      "status": "DEPRECATED"
    }
  ]
}', true);
    }

    private function mockListWebHookEventsTypesResponse(): array
    {
        return Utils::jsonDecode('{
  "event_types": [
    {
      "name": "PAYMENT.AUTHORIZATION.CREATED",
      "description": "A payment authorization was created.",
      "status": "ENABLED",
      "service": "amqpaymentwebhookd",
      "owner": "Webhooks",
      "contact": "livesupport@example.com"
    },
    {
      "name": "PAYMENT.AUTHORIZATION.VOIDED",
      "description": "A payment authorization was voided.",
      "status": "ENABLED",
      "service": "amqpaymentwebhookd",
      "owner": "Webhooks",
      "contact": "livesupport@example.com"
    },
    {
      "name": "PAYMENT.CAPTURE.COMPLETED",
      "description": "A capture payment was completed.",
      "status": "ENABLED",
      "service": "amqpaymentwebhookd",
      "owner": "Webhooks",
      "contact": "livesupport@example.com"
    }
  ]
}', true);
    }

    private function mockWebHookEventsListResponse(): array
    {
        return Utils::jsonDecode('{
  "events": [
    {
      "id": "8PT597110X687430LKGECATA",
      "create_time": "2013-06-25T21:41:28Z",
      "resource_type": "authorization",
      "event_version": "1.0",
      "event_type": "PAYMENT.AUTHORIZATION.CREATED",
      "summary": "A payment authorization was created",
      "resource_version": "1.0",
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
      },
      "links": [
        {
          "href": "https://api.paypal.com/v1/notfications/webhooks-events/8PT597110X687430LKGECATA",
          "rel": "self",
          "method": "GET"
        },
        {
          "href": "https://api.paypal.com/v1/notfications/webhooks-events/8PT597110X687430LKGECATA/resend",
          "rel": "resend",
          "method": "POST"
        }
      ]
    },
    {
      "id": "HTSPGS710X687430LKGECATA",
      "create_time": "2013-06-25T21:41:28Z",
      "resource_type": "authorization",
      "event_version": "1.0",
      "event_type": "PAYMENT.AUTHORIZATION.CREATED",
      "summary": "A payment authorization was created",
      "resource_version": "1.0",
      "resource": {
        "id": "HATH7S72EK520411B",
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
        "parent_payment": "PAY-ALDSFJ64YD343335CKHFA4AY",
        "valid_until": "2013-07-24T21:39:15Z",
        "links": [
          {
            "href": "https://api.paypal.com/v1/payments/authorization/HATH7S72EK520411B",
            "rel": "self",
            "method": "GET"
          },
          {
            "href": "https://api.paypal.com/v1/payments/authorization/HATH7S72EK520411B/capture",
            "rel": "capture",
            "method": "POST"
          },
          {
            "href": "https://api.paypal.com/v1/payments/authorization/HATH7S72EK520411B/void",
            "rel": "void",
            "method": "POST"
          },
          {
            "href": "https://api.paypal.com/v1/payments/payment/PAY-HATH7S72EK520411B",
            "rel": "parent_payment",
            "method": "GET"
          }
        ]
      },
      "links": [
        {
          "href": "https://api.paypal.com/v1/notfications/webhooks-events/HTSPGS710X687430LKGECATA",
          "rel": "self",
          "method": "GET"
        },
        {
          "href": "https://api.paypal.com/v1/notfications/webhooks-events/HTSPGS710X687430LKGECATA/resend",
          "rel": "resend",
          "method": "POST"
        }
      ]
    }
  ],
  "count": 2,
  "links": [
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks-events/?start_time=2014-08-04T12:46:47-07:00&amp;end_time=2014-09-18T12:46:47-07:00&amp;page_size=2&amp;move_to=next&amp;index_time=2014-09-17T23:07:35Z&amp;index_id=3",
      "rel": "next",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/notifications/webhooks-events/?start_time=2014-08-04T12:46:47-07:00&amp;end_time=2014-09-18T12:46:47-07:00&amp;page_size=2&amp;move_to=previous&amp;index_time=2014-09-17T23:07:35Z&amp;index_id=0",
      "rel": "previous",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockGetWebHookEventResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "8PT597110X687430LKGECATA",
  "create_time": "2013-06-25T21:41:28Z",
  "resource_type": "authorization",
  "event_version": "1.0",
  "event_type": "PAYMENT.AUTHORIZATION.CREATED",
  "summary": "A payment authorization was created",
  "resource_version": "1.0",
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
  },
  "links": [
    {
      "href": "https://api.paypal.com/v1/notfications/webhooks-events/8PT597110X687430LKGECATA",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/notfications/webhooks-events/8PT597110X687430LKGECATA/resend",
      "rel": "resend",
      "method": "POST"
    }
  ]
}', true);
    }

    private function mockResendWebHookEventNotificationResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "8PT597110X687430LKGECATA",
  "create_time": "2013-06-25T21:41:28Z",
  "resource_type": "authorization",
  "event_version": "1.0",
  "event_type": "PAYMENT.AUTHORIZATION.CREATED",
  "summary": "A payment authorization was created",
  "resource_version": "1.0",
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
  },
  "links": [
    {
      "href": "https://api.paypal.com/v1/notfications/webhooks-events/8PT597110X687430LKGECATA",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/notfications/webhooks-events/8PT597110X687430LKGECATA/resend",
      "rel": "resend",
      "method": "POST"
    }
  ]
}', true);
    }

    private function mockVerifyWebHookSignatureResponse(): array
    {
        return Utils::jsonDecode('{
  "verification_status": "SUCCESS"
}', true);
    }
}
