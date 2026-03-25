<?php

namespace Blendbyte\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait Identity
{
    private function mockShowProfileInfoResponse(): array
    {
        return Utils::jsonDecode('{
  "address": {
    "street_address": "7917394 Annursnac Hill Road Unit 0C",
    "locality": "Ventura",
    "region": "CA",
    "postal_code": "93003",
    "country": "US"
  }
}', true);
    }

    private function mocklistUsersResponse(): array
    {
        return Utils::jsonDecode('{
  "schemas": [
    "http://example.com"
  ],
  "startIndex": 1,
  "itemsPerPage": 1,
  "totalResults": 5000,
  "Resources": [
    {
      "schemas": [
        "http://example.com"
      ],
      "externalId": "string",
      "userName": "string",
      "name": {
        "familyName": "string",
        "givenName": "string",
        "middleName": "string",
        "honorificPrefix": "string",
        "honorificSuffix": "string"
      },
      "active": true,
      "emails": [
        {
          "type": "work",
          "primary": true,
          "value": "string"
        }
      ],
      "phoneNumbers": [
        {
          "value": "string",
          "type": "work",
          "primary": true
        }
      ],
      "addresses": [
        {
          "streetAddress": "string",
          "locality": "string",
          "region": "string",
          "postalCode": "string",
          "type": "work",
          "country": "string"
        }
      ],
      "entitlements": [
        {
          "value": "string"
        }
      ],
      "id": "string",
      "meta": {
        "resourceType": "User",
        "location": "http://example.com",
        "created": "string",
        "lastModified": "string"
      },
      "preferredLanguage": "string",
      "timezone": "string"
    }
  ]
}', true);
    }

    private function mocklistUserResponse(): array
    {
        return Utils::jsonDecode('{
  "schemas": [
    "http://example.com"
  ],
  "externalId": "string",
  "userName": "string",
  "name": {
    "familyName": "string",
    "givenName": "string",
    "middleName": "string",
    "honorificPrefix": "string",
    "honorificSuffix": "string"
  },
  "active": true,
  "emails": [
    {
      "type": "work",
      "primary": true,
      "value": "string"
    }
  ],
  "phoneNumbers": [
    {
      "value": "string",
      "type": "work",
      "primary": true
    }
  ],
  "addresses": [
    {
      "streetAddress": "string",
      "locality": "string",
      "region": "string",
      "postalCode": "string",
      "type": "work",
      "country": "string"
    }
  ],
  "entitlements": [
    {
      "value": "string"
    }
  ],
  "id": "string",
  "meta": {
    "resourceType": "User",
    "location": "http://example.com",
    "created": "string",
    "lastModified": "string"
  },
  "preferredLanguage": "string",
  "timezone": "string"
}', true);
    }

    private function mockCreateMerchantApplicationResponse(): array
    {
        return Utils::jsonDecode('{
  "client_id": "AeTeCqaPp7JZBfUUb2d21cQ2KqyQGVhonfiUOJu99kgLhFFSrE59ruvhLOT4K3NzQoErgsUH6MY9uRqD",
  "client_secret": "cf136dc3c1fc93f31185e5885805d",
  "client_id_issued_at": 2893256800,
  "client_secret_expires_at": 2893276800,
  "redirect_uris": [
    "https://example.com/callback",
    "https://example.com/callback2"
  ],
  "grant_types": [
    "authorization_code",
    "refresh_token"
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

    private function mockGetClientTokenResponse(): array
    {
        return Utils::jsonDecode('{
  "client_token": "eyJicmFpbnRyZWUiOnsiYXV0aG9yaXphdGlvbkZpbmdlcnByaW50IjoiYjA0MWE2M2JlMTM4M2NlZGUxZTI3OWFlNDlhMWIyNzZlY2FjOTYzOWU2NjlhMGIzODQyYTdkMTY3NzcwYmY0OHxtZXJjaGFudF9pZD1yd3dua3FnMnhnNTZobTJuJnB1YmxpY19rZXk9czlic3BuaGtxMmYzaDk0NCZjcmVhdGVkX2F0PTIwMTgtMTEtMTRUMTE6MTg6MDAuMTU3WiIsInZlcnNpb24iOiIzLXBheXBhbCJ9LCJwYXlwYWwiOnsiYWNjZXNzVG9rZW4iOiJBMjFBQUhNVExyMmctVDlhSTJacUZHUmlFZ0ZFZGRHTGwxTzRlX0lvdk9ESVg2Q3pSdW5BVy02TzI2MjdiWUJ2cDNjQ0FNWi1lTFBNc2NDWnN0bDUyNHJyUGhUQklJNlBBIn19",
  "expires_in": 3600
}', true);
    }
}
