<?php

namespace Blendbyte\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait PaymentAuthorizations
{
    /**
     * @return array
     */
    private function mockCaptureAuthorizedPaymentParams(): array
    {
        return Utils::jsonDecode('{
  "amount": {
    "value": "10.99",
    "currency_code": "USD"
  },
  "invoice_id": "INVOICE-123",
  "note_to_payer": "Payment is due",
  "final_capture": true
}', true);
    }

    /**
     * @return array
     */
    private function mockReAuthorizeAuthorizedPaymentParams(): array
    {
        return Utils::jsonDecode('{
  "amount": {
    "value": "10.99",
    "currency_code": "USD"
  }
}', true);
    }
}
