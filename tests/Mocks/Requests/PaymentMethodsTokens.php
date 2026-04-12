<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait PaymentMethodsTokens
{
    private function mockCreatePaymentSetupTokensParams(): array
    {
        return Utils::jsonDecode('{
            "payment_source": {
              "card": {
                "number": "4111111111111111",
                "expiry": "2027-02",
                "name": "John Doe",
                "billing_address": {
                  "address_line_1": "2211 N First Street",
                  "address_line_2": "17.3.160",
                  "admin_area_1": "CA",
                  "admin_area_2": "San Jose",
                  "postal_code": "95131",
                  "country_code": "US"
                },
                "experience_context": {
                  "brand_name": "YourBrandName",
                  "locale": "en-US",
                  "return_url": "https://example.com/returnUrl",
                  "cancel_url": "https://example.com/cancelUrl"
                }
              }
            }
          }', true);
    }

    private function mockCreatePaymentSetupPayPalParams(): array
    {
        return Utils::jsonDecode('{
          "payment_source": {
              "paypal": {
                  "description": "Description for PayPal to be shown to PayPal payer",
                  "shipping": {
                      "name": {
                          "full_name": "Firstname Lastname"
                      },
                      "address": {
                          "address_line_1": "2211 N First Street",
                          "address_line_2": "Building 17",
                          "admin_area_2": "San Jose",
                          "admin_area_1": "CA",
                          "postal_code": "95131",
                          "country_code": "US"
                      }
                  },
                  "permit_multiple_payment_tokens": false,
                  "usage_pattern": "IMMEDIATE",
                  "usage_type": "MERCHANT",
                  "customer_type": "CONSUMER",
                  "experience_context": {
                      "shipping_preference": "SET_PROVIDED_ADDRESS",
                      "payment_method_preference":    "IMMEDIATE_PAYMENT_REQUIRED",
                      "brand_name": "EXAMPLE INC",
                      "locale": "en-US",
                      "return_url": "https://example.com/returnUrl",
                      "cancel_url": "https://example.com/cancelUrl"
                  }
              }
          }
      }', true);
    }
}
