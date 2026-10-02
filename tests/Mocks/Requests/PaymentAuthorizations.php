<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;


trait PaymentAuthorizations
{
    private function mockCaptureAuthorizedPaymentParams(): array
    {
        return json_decode('{
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
        return json_decode('{
  "amount": {
    "value": "10.99",
    "currency_code": "USD"
  }
}', true);
    }
}
