<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait Orders
{
    public function mockCreateOrdersResponse(): array
    {
        return Utils::jsonDecode('{
        "id": "5O190127TN364715T",
        "status": "CREATED",
        "links": [
          {
            "href": "https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T",
            "rel": "self",
            "method": "GET"
          },
          {
            "href": "https://www.paypal.com/checkoutnow?token=5O190127TN364715T",
            "rel": "approve",
            "method": "GET"
          },
          {
            "href": "https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T",
            "rel": "update",
            "method": "PATCH"
          },
          {
            "href": "https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T/capture",
            "rel": "capture",
            "method": "POST"
          }
        ]
      }', true);
    }

    /**
     * @return empty
     */
    public function mockUpdateOrdersResponse()
    {
        return '';
    }

    public function mockOrderDetailsResponse(): array
    {
        return Utils::jsonDecode('{
        "id": "5O190127TN364715T",
        "status": "PAYER_ACTION_REQUIRED",
        "intent": "CAPTURE",
        "payment_source": {
          "alipay": {
            "name": "John Doe",
            "country_code": "C2"
          }
        },
        "purchase_units": [
          {
            "reference_id": "d9f80740-38f0-11e8-b467-0ed5f89f718b",
            "amount": {
              "currency_code": "USD",
              "value": "100.00"
            },
            "payee": {
              "email_address": "payee@example.com"
            }
          }
        ],
        "create_time": "2018-04-01T21:18:49Z",
        "links": [
          {
            "href": "https://www.paypal.com/payment/alipay?token=5O190127TN364715T",
            "rel": "payer-action",
            "method": "GET"
          },
          {
            "href": "https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T",
            "rel": "self",
            "method": "GET"
          }
        ]
      }', true);
    }

    public function mockOrderPaymentAuthorizedResponse(): array
    {
        return Utils::jsonDecode('{
        "id": "5O190127TN364715T",
        "status": "COMPLETED",
        "payer": {
          "name": {
            "given_name": "John",
            "surname": "Doe"
          },
          "email_address": "customer@example.com",
          "payer_id": "QYR5Z8XDVJNXQ"
        },
        "purchase_units": [
          {
            "reference_id": "d9f80740-38f0-11e8-b467-0ed5f89f718b",
            "shipping": {
              "address": {
                "address_line_1": "2211 N First Street",
                "address_line_2": "Building 17",
                "admin_area_2": "San Jose",
                "admin_area_1": "CA",
                "postal_code": "95131",
                "country_code": "US"
              }
            },
            "payments": {
              "authorizations": [
                {
                  "id": "0AW2184448108334S",
                  "status": "CREATED",
                  "amount": {
                    "currency_code": "USD",
                    "value": "100.00"
                  },
                  "seller_protection": {
                    "status": "ELIGIBLE",
                    "dispute_categories": [
                      "ITEM_NOT_RECEIVED",
                      "UNAUTHORIZED_TRANSACTION"
                    ]
                  },
                  "expiration_time": "2018-05-01T21:20:49Z",
                  "create_time": "2018-04-01T21:20:49Z",
                  "update_time": "2018-04-01T21:20:49Z",
                  "links": [
                    {
                      "href": "https://api-m.paypal.com/v2/payments/authorizations/0AW2184448108334S",
                      "rel": "self",
                      "method": "GET"
                    },
                    {
                      "href": "https://api-m.paypal.com/v2/payments/authorizations/0AW2184448108334S/capture",
                      "rel": "capture",
                      "method": "POST"
                    },
                    {
                      "href": "https://api-m.paypal.com/v2/payments/authorizations/0AW2184448108334S/void",
                      "rel": "void",
                      "method": "POST"
                    },
                    {
                      "href": "https://api-m.paypal.com/v2/payments/authorizations/0AW2184448108334S/reauthorize",
                      "rel": "reauthorize",
                      "method": "POST"
                    }
                  ]
                }
              ]
            }
          }
        ],
        "links": [
          {
            "href": "https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T",
            "rel": "self",
            "method": "GET"
          }
        ]
      }', true);
    }

    public function mockOrderPaymentCapturedResponse(): array
    {
        return Utils::jsonDecode('{
        "id": "5O190127TN364715T",
        "status": "COMPLETED",
        "payer": {
          "name": {
            "given_name": "John",
            "surname": "Doe"
          },
          "email_address": "customer@example.com",
          "payer_id": "QYR5Z8XDVJNXQ"
        },
        "purchase_units": [
          {
            "reference_id": "d9f80740-38f0-11e8-b467-0ed5f89f718b",
            "shipping": {
              "address": {
                "address_line_1": "2211 N First Street",
                "address_line_2": "Building 17",
                "admin_area_2": "San Jose",
                "admin_area_1": "CA",
                "postal_code": "95131",
                "country_code": "US"
              }
            },
            "payments": {
              "captures": [
                {
                  "id": "3C679366HH908993F",
                  "status": "COMPLETED",
                  "amount": {
                    "currency_code": "USD",
                    "value": "100.00"
                  },
                  "seller_protection": {
                    "status": "ELIGIBLE",
                    "dispute_categories": [
                      "ITEM_NOT_RECEIVED",
                      "UNAUTHORIZED_TRANSACTION"
                    ]
                  },
                  "final_capture": true,
                  "disbursement_mode": "INSTANT",
                  "seller_receivable_breakdown": {
                    "gross_amount": {
                      "currency_code": "USD",
                      "value": "100.00"
                    },
                    "paypal_fee": {
                      "currency_code": "USD",
                      "value": "3.00"
                    },
                    "net_amount": {
                      "currency_code": "USD",
                      "value": "97.00"
                    }
                  },
                  "create_time": "2018-04-01T21:20:49Z",
                  "update_time": "2018-04-01T21:20:49Z",
                  "links": [
                    {
                      "href": "https://api-m.paypal.com/v2/payments/captures/3C679366HH908993F",
                      "rel": "self",
                      "method": "GET"
                    },
                    {
                      "href": "https://api-m.paypal.com/v2/payments/captures/3C679366HH908993F/refund",
                      "rel": "refund",
                      "method": "POST"
                    }
                  ]
                }
              ]
            }
          }
        ],
        "links": [
          {
            "href": "https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T",
            "rel": "self",
            "method": "GET"
          }
        ]
      }', true);
    }

    private function mockAddTrackingForOrderResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "5O190127TN364715T",
  "status": "COMPLETED",
  "purchase_units": [
    {
      "reference_id": "d9f80740-38f0-11e8-b467-0ed5f89f718b",
      "shipping": {
        "trackers": [
          {
            "id": "5O190127TN364715T-443844607820",
            "status": "SHIPPED",
            "tracking_number": "443844607820",
            "carrier": "FEDEX"
          }
        ]
      }
    }
  ]
}', true);
    }

    private function mockConfirmOrderResponse()
    {
        return Utils::jsonDecode('{
  "id": "5O190127TN364715T",
  "status": "PAYER_ACTION_REQUIRED",
  "payment_source": {
    "paypal": {
      "name": {
        "given_name": "John",
        "surname": "Doe"
      },
      "email_address": "customer@example.com"
    }
  },
  "payer": {
    "name": {
      "given_name": "John",
      "surname": "Doe"
    },
    "email_address": "customer@example.com"
  },
  "links": [
    {
      "href": "https://api.paypal.com/v2/checkout/orders/5O190127TN364715T",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://www.paypal.com/checkoutnow?token=5O190127TN364715T",
      "rel": "payer-action",
      "method": "GET"
    }
  ]
}', true);
    }
}
