<?php

namespace Blendbyte\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait PaymentCaptures
{
    /**
     * @return array
     */
    private function mockRefundCapturedPaymentParams(): array
    {
        return Utils::jsonDecode('{
  "amount": {
    "value": "10.99",
    "currency_code": "USD"
  },
  "invoice_id": "INVOICE-123",
  "note_to_payer": "Defective product"
}', true);
    }
}
