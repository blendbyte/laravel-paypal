<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait CatalogProducts
{
    private function mockCreateCatalogProductsResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "PROD-XYAB12ABSB7868434",
  "name": "Video Streaming Service",
  "description": "Video streaming service",
  "type": "SERVICE",
  "category": "SOFTWARE",
  "image_url": "https://example.com/streaming.jpg",
  "home_url": "https://example.com/home",
  "create_time": "2020-01-10T21:20:49Z",
  "update_time": "2020-01-10T21:20:49Z",
  "links": [
    {
      "href": "https://api.paypal.com/v1/catalogs/products/72255d4849af8ed6e0df1173",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/catalogs/products/72255d4849af8ed6e0df1173",
      "rel": "edit",
      "method": "PATCH"
    }
  ]
}', true);
    }

    private function mockListCatalogProductsResponse(): array
    {
        return Utils::jsonDecode('{
  "total_items": 20,
  "total_pages": 1,
  "products": [
    {
      "id": "72255d4849af8ed6e0df1173",
      "name": "Video Streaming Service",
      "description": "Video streaming service",
      "create_time": "2018-12-10T21:20:49Z",
      "links": [
        {
          "href": "https://api.paypal.com/v1/catalogs/products/72255d4849af8ed6e0df1173",
          "rel": "self",
          "method": "GET"
        }
      ]
    },
    {
      "id": "PROD-XYAB12ABSB7868434",
      "name": "Video Streaming Service",
      "description": "Audio streaming service",
      "create_time": "2018-12-10T21:20:49Z",
      "links": [
        {
          "href": "https://api.paypal.com/v1/catalogs/products/125d4849af8ed6e0df18",
          "rel": "self",
          "method": "GET"
        }
      ]
    }
  ],
  "links": [
    {
      "href": "https://api.paypal.com/v1/catalogs/products?page_size=2&page=1",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/catalogs/products?page_size=2&page=2",
      "rel": "next",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/catalogs/products?page_size=2&page=10",
      "rel": "last",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockGetCatalogProductsResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "72255d4849af8ed6e0df1173",
  "name": "Video Streaming Service",
  "description": "Video streaming service",
  "type": "SERVICE",
  "category": "SOFTWARE",
  "image_url": "https://example.com/streaming.jpg",
  "home_url": "https://example.com/home",
  "create_time": "2018-12-10T21:20:49Z",
  "update_time": "2018-12-10T21:20:49Z",
  "links": [
    {
      "href": "https://api.paypal.com/v1/catalogs/products/72255d4849af8ed6e0df1173",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v1/catalogs/products/72255d4849af8ed6e0df1173",
      "rel": "edit",
      "method": "PATCH"
    }
  ]
}', true);
    }

    private function mockGetCatalogProductsErrorResponse(): array
    {
        return Utils::jsonDecode('{
    "error": {
        "name": "INVALID_REQUEST",
        "message": "Request is not well-formed, syntactically incorrect, or violates schema.",
        "debug_id": "b2aaac7fe91d1",
        "details": [
            {
                "field": "\/type",
                "location": "body",
                "issue": "MISSING_REQUIRED_PARAMETER",
                "description": "A required field is missing."
            }
        ],
        "links": [
            {
                "href": "https:\/\/developer.paypal.com\/docs\/api\/v1\/billing\/subscriptions#INVALID_REQUEST",
                "rel": "information_link",
                "method": "GET"
            }
        ]
    }
}', true);
    }
}
