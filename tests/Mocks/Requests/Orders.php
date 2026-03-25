<?php

namespace Blendbyte\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait Orders
{
    /*
    * @return array
    */
    private function createOrderParams(): array
    {
        return Utils::jsonDecode('{
            "intent": "CAPTURE",
            "purchase_units": [
              {
                "amount": {
                  "currency_code": "USD",
                  "value": "100.00"
                }
              }
            ]
          }', true);
    }

    /*
    * @return array
    */
    private function updateOrderParams(): array
    {
        return Utils::jsonDecode('[
        {
          "op": "replace",
          "path": "/purchase_units/@reference_id==\'PUHF\'/shipping/address",
          "value": {
            "address_line_1": "123 Townsend St",
            "address_line_2": "Floor 6",
            "admin_area_2": "San Francisco",
            "admin_area_1": "CA",
            "postal_code": "94107",
            "country_code": "US"
          }
        }
      ]', true);
    }
}
