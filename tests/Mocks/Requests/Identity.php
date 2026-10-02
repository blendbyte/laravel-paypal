<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;


trait Identity
{
    private function mockCreateMerchantApplicationParams(): array
    {
        return json_decode('{
  "redirect_uris": [
    "https://example.com/callback",
    "https://example.com/callback2"
  ],
  "client_name": "AGGREGATOR",
  "logo_uri": "https://example.com/logo.png",
  "contacts": [
    "facilitator@example.com",
    "merchant@example.com"
  ],
  "policy_uri": "https://example.com/policyuri",
  "tos_uri": "https://example.com/tosuri",
  "scope": "profile email address",
  "token_endpoint_auth_method": "client_secret_basic",
  "jwks_uri": "https://example.com/my_public_keys.jwks"
}', true);
    }

    private function mockSetAccountPropertiesParams(): array
    {
        return json_decode('{
    "categories": [
      {
        "name": "PAYMENT",
        "groups": [
          {
            "name": "AUTH_SETTLE",
            "preferences": [
              {
                "name": "ENABLE_ENHANCED_AUTH_SETTLE",
                "value": "true"
              }
            ]
          }
        ]
      }
    ]
  }', true);
    }
}
