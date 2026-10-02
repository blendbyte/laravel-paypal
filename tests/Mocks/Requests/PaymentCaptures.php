<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;


trait PaymentCaptures
{
    private function mockRefundCapturedPaymentParams(): array
    {
        return json_decode('{
  "amount": {
    "value": "10.99",
    "currency_code": "USD"
  },
  "invoice_id": "INVOICE-123",
  "note_to_payer": "Defective product"
}', true);
    }
}
