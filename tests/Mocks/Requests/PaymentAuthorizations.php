<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait PaymentAuthorizations
{
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
