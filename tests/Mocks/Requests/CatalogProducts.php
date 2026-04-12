<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait CatalogProducts
{
    private function createProductParams(): array
    {
        return Utils::jsonDecode('{
          "name": "Video Streaming Service",
          "description": "Video streaming service",
          "type": "SERVICE",
          "category": "SOFTWARE",
          "image_url": "https://example.com/streaming.jpg",
          "home_url": "https://example.com/home"
        }', true);
    }

    private function updateProductParams(): array
    {
        return Utils::jsonDecode('[
          {
            "op": "replace",
            "path": "/description",
            "value": "Premium video streaming service"
          }
        ]', true);
    }
}
