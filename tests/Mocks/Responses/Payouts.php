<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait Payouts
{
    private function mockCreateBatchPayoutResponse(): array
    {
        return Utils::jsonDecode('{
  "batch_header": {
    "sender_batch_header": {
      "sender_batch_id": "Payouts_2018_100008",
      "email_subject": "You have a payout!",
      "email_message": "You have received a payout! Thanks for using our service!"
    },
    "payout_batch_id": "5UXD2E8A7EBQJ",
    "batch_status": "PENDING"
  }
}', true);
    }

    private function showBatchPayoutResponse(): array
    {
        return Utils::jsonDecode('{
  "batch_header": {
    "payout_batch_id": "FYXMPQTX4JC9N",
    "batch_status": "PROCESSING",
    "time_created": "2014-01-27T10:17:00Z",
    "time_completed": "2014-01-27T11:17:39.00Z",
    "sender_batch_header": {
      "sender_batch_id": "Payouts_2018_100009",
      "email_subject": "You have a payout!"
    },
    "amount": {
      "value": "438.35",
      "currency": "USD"
    },
    "fees": {
      "value": "5.84",
      "currency": "USD"
    }
  },
  "items": [
    {
      "payout_item_id": "DUCD8GC3VUKVE",
      "transaction_id": "6KA23440H1057442S",
      "transaction_status": "SUCCESS",
      "payout_batch_id": "FYXMPQTX4JC9N",
      "payout_item_fee": {
        "currency": "USD",
        "value": "1.00"
      },
      "payout_item": {
        "recipient_type": "EMAIL",
        "amount": {
          "value": "65.24",
          "currency": "USD"
        },
        "note": "Thanks for your patronage!",
        "receiver": "receiver@example.com",
        "sender_item_id": "14Feb_978"
      },
      "time_processed": "2014-01-27T10:18:32Z"
    },
    {
      "payout_item_id": "LGMEPRKTK7FQA",
      "transaction_id": "8K128187J1102003K",
      "transaction_status": "SUCCESS",
      "payout_batch_id": "FYXMPQTX4JC9N",
      "payout_item_fee": {
        "currency": "USD",
        "value": "1.00"
      },
      "payout_item": {
        "recipient_type": "EMAIL",
        "amount": {
          "value": "59.87",
          "currency": "USD"
        },
        "note": "Thanks for your patronage!",
        "receiver": "receiver2@example.com",
        "sender_item_id": "14Feb_321"
      },
      "time_processed": "2014-01-27T10:18:15Z"
    },
    {
      "payout_item_id": "BQ8GT9VG64EFS",
      "transaction_id": "57382391EC1682714",
      "transaction_status": "SUCCESS",
      "payout_batch_id": "FYXMPQTX4JC9N",
      "payout_item_fee": {
        "currency": "USD",
        "value": "1.00"
      },
      "payout_item": {
        "recipient_type": "EMAIL",
        "amount": {
          "value": "59.87",
          "currency": "USD"
        },
        "note": "Thanks for your patronage!",
        "receiver": "receiver3@example.com",
        "sender_item_id": "14Feb_239"
      },
      "time_processed": "2014-01-27T10:17:15Z"
    },
    {
      "payout_item_id": "LHKZN4VT93L2Q",
      "transaction_id": "1LG71547D1353984N",
      "transaction_status": "SUCCESS",
      "payout_batch_id": "FYXMPQTX4JC9N",
      "payout_item_fee": {
        "value": "USD",
        "currency": "0.75"
      },
      "payout_item": {
        "recipient_type": "EMAIL",
        "amount": {
          "value": "19.87",
          "currency": "USD"
        },
        "note": "Thanks for your patronage!",
        "receiver": "receiver4@example.com",
        "sender_item_id": "14Feb_235"
      },
      "time_processed": "2014-01-27T10:17:25Z"
    },
    {
      "payout_item_id": "4ZF3VZHHTQJG6",
      "transaction_id": "4BD48613EX3256543",
      "transaction_status": "SUCCESS",
      "payout_batch_id": "FYXMPQTX4JC9N",
      "payout_item_fee": {
        "currency": "USD",
        "value": "0.75"
      },
      "payout_item": {
        "recipient_type": "EMAIL",
        "amount": {
          "value": "9.87",
          "currency": "USD"
        },
        "note": "Thanks for your patronage!",
        "receiver": "receiver@example.com",
        "sender_item_id": "14Feb_234"
      },
      "time_processed": "2014-01-27T10:17:37Z"
    },
    {
      "payout_item_id": "DTCJCQ6LMH8JQ",
      "transaction_id": "25F63571CL9929422",
      "transaction_status": "SUCCESS",
      "payout_item_fee": {
        "currency": "USD",
        "value": "2.35"
      },
      "payout_batch_id": "FYXMPQTX4JC9N",
      "payout_item": {
        "recipient_type": "PHONE",
        "amount": {
          "value": "112.34",
          "currency": "USD"
        },
        "note": "Thanks for your support!",
        "receiver": "91-734-234-1234",
        "sender_item_id": "14Feb_235"
      },
      "time_processed": "2014-01-27T10:17:52Z"
    },
    {
      "payout_item_id": "GSBDQHUAUD44A",
      "transaction_id": "53R03517P98080414",
      "transaction_status": "SUCCESS",
      "payout_batch_id": "FYXMPQTX4JC9N",
      "payout_item_fee": {
        "currency": "USD",
        "value": "2.5"
      },
      "payout_item": {
        "recipient_type": "PHONE",
        "amount": {
          "value": "5.32",
          "currency": "USD"
        },
        "note": "Thanks for your patronage!",
        "receiver": "408X234-1234",
        "sender_item_id": "14Feb_235"
      },
      "time_processed": "2014-01-27T10:17:41Z"
    }
  ],
  "links": [
    {
      "rel": "self",
      "href": "https://api-m.sandbox.paypal.com/v1/payments/payouts/FYXMPQTX4JC9N?page_size=1000&page=1",
      "method": "GET"
    }
  ]
}', true);
    }

    private function showBatchPayoutItemResponse(): array
    {
        return Utils::jsonDecode('{
  "payout_item_id": "8AELMXH8UB2P8",
  "transaction_id": "0C413693MN970190K",
  "activity_id": "0E158638XS0329106",
  "transaction_status": "SUCCESS",
  "payout_item_fee": {
    "currency": "USD",
    "value": "0.35"
  },
  "payout_batch_id": "Q8KVJG9TZTNN4",
  "payout_item": {
    "amount": {
      "value": "9.87",
      "currency": "USD"
    },
    "recipient_type": "EMAIL",
    "note": "Thanks for your patronage!",
    "receiver": "receiver@example.com",
    "sender_item_id": "14Feb_234"
  },
  "time_processed": "2018-01-27T10:17:41Z",
  "links": [
    {
      "rel": "self",
      "href": "https://api-m.sandbox.paypal.com/v1/payments/payouts-item/8AELMXH8UB2P8",
      "method": "GET"
    },
    {
      "href": "https://api-m.sandbox.paypal.com/v1/payments/payouts/Q8KVJG9TZTNN4",
      "rel": "batch",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockCancelUnclaimedBatchItemResponse(): array
    {
        return Utils::jsonDecode('{
  "payout_item_id": "5KUDKLF8SDC7S",
  "transaction_id": "1DG93452WK758815H",
  "activity_id": "0E158638XS0329101",
  "transaction_status": "RETURNED",
  "payout_item_fee": {
    "currency": "USD",
    "value": "0.35"
  },
  "payout_batch_id": "CQMWKDQF5GFLL",
  "sender_batch_id": "Payouts_2018_100006",
  "payout_item": {
    "recipient_type": "EMAIL",
    "amount": {
      "value": "9.87",
      "currency": "USD"
    },
    "note": "Thanks for your patronage!",
    "receiver": "receiver@example.com",
    "sender_item_id": "14Feb_234"
  },
  "time_processed": "2018-01-27T10:17:41Z",
  "errors": {
    "name": "RECEIVER_UNREGISTERED",
    "message": "Receiver is unregistered",
    "information_link": "https://developer.paypal.com/docs/api/payments.payouts-batch#errors"
  },
  "links": [
    {
      "rel": "self",
      "href": "https://api-m.sandbox.paypal.com/v1/payments/payouts-item/5KUDKLF8SDC7S",
      "method": "GET"
    },
    {
      "rel": "batch",
      "href": "https://api-m.sandbox.paypal.com/v1/payments/payouts/CQMWKDQF5GFLL",
      "method": "GET"
    }
  ]
}', true);
    }
}
