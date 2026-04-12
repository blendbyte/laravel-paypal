<?php

namespace Srmklive\PayPal\Tests\Mocks\Requests;

use GuzzleHttp\Utils;

trait InvoicesTemplates
{
    private function mockCreateInvoiceTemplateParams(): array
    {
        return Utils::jsonDecode('{
  "default_template": true,
  "template_info": {
    "configuration": {
      "tax_calculated_after_discount": true,
      "tax_inclusive": false,
      "allow_tip": true,
      "partial_payment": {
        "allow_partial_payment": true,
        "minimum_amount_due": {
          "currency_code": "USD",
          "value": "20.00"
        }
      }
    },
    "detail": {
      "reference": "deal-ref",
      "note": "Thank you for your business.",
      "currency_code": "USD",
      "terms_and_conditions": "No refunds after 30 days.",
      "memo": "This is a long contract",
      "attachments": [
        {
          "id": "Screen Shot 2018-11-23 at 16.45.01.png",
          "reference_url": "https://api.paypal.com/invoice/payerView/attachments/RkG9ggQbd4Mwm1tYdcF6uuixfFTFq32bBdbE1VbtQLdKSoS2ZOYpfjw9gPp7eTrZmVaFaDWzixHXm-OXWHbmigHigHzURDxJs8IIKqcqP8jawnBEZcraEAPVMULxf5iTyOSpAUc2ugW0PWdwDbM6mg-guFAUyj3Z98H7htWNjQY95jb9heOlcSXUe.sbDUR9smAszzzJoA1NXT6rEEegwQ&version=1&sig=JNODB0xEayW8txMQm6ZsIwDnd4eh3hd6ijiRLi4ipHE"
        }
      ],
      "payment_term": {
        "term_type": "NET_10"
      }
    },
    "invoicer": {
      "name": {
        "given_name": "David",
        "surname": "Larusso"
      },
      "address": {
        "address_line_1": "1234 First Street",
        "address_line_2": "337673 Hillside Court",
        "admin_area_2": "Anytown",
        "admin_area_1": "CA",
        "postal_code": "98765",
        "country_code": "US"
      },
      "email_address": "merchant@example.com",
      "phones": [
        {
          "country_code": "001",
          "national_number": "4085551234",
          "phone_type": "MOBILE"
        }
      ],
      "website": "www.test.com",
      "tax_id": "ABcNkWSfb5ICTt73nD3QON1fnnpgNKBy-Jb5SeuGj185MNNw6g",
      "logo_url": "https://example.com/logo.PNG",
      "additional_notes": "2-4"
    },
    "primary_recipients": [
      {
        "billing_info": {
          "name": {
            "given_name": "Stephanie",
            "surname": "Meyers"
          },
          "address": {
            "address_line_1": "1234 Main Street",
            "admin_area_2": "Anytown",
            "admin_area_1": "CA",
            "postal_code": "98765",
            "country_code": "US"
          },
          "email_address": "bill-me@example.com",
          "phones": [
            {
              "country_code": "001",
              "national_number": "4884551234",
              "phone_type": "MOBILE"
            }
          ],
          "additional_info": "add-info"
        },
        "shipping_info": {
          "name": {
            "given_name": "Stephanie",
            "surname": "Meyers"
          },
          "address": {
            "address_line_1": "1234 Main Street",
            "admin_area_2": "Anytown",
            "admin_area_1": "CA",
            "postal_code": "98765",
            "country_code": "US"
          }
        }
      }
    ],
    "additional_recipients": [
      "inform-me@example.com"
    ],
    "items": [
      {
        "name": "Yoga Mat",
        "description": "new watch",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "50.00"
        },
        "tax": {
          "name": "Sales Tax",
          "percent": "7.25"
        },
        "discount": {
          "percent": "5"
        },
        "unit_of_measure": "QUANTITY"
      },
      {
        "name": "Yoga T Shirt",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "10.00"
        },
        "tax": {
          "name": "Sales Tax",
          "percent": "7.25"
        },
        "discount": {
          "amount": {
            "currency_code": "USD",
            "value": "5.00"
          }
        },
        "unit_of_measure": "QUANTITY"
      }
    ],
    "amount": {
      "currency_code": "USD",
      "value": "74.21",
      "breakdown": {
        "custom": {
          "label": "Packing Charges",
          "amount": {
            "currency_code": "USD",
            "value": "10.00"
          }
        },
        "shipping": {
          "amount": {
            "currency_code": "USD",
            "value": "10.00"
          },
          "tax": {
            "name": "Sales Tax",
            "percent": "7.25"
          }
        },
        "discount": {
          "invoice_discount": {
            "percent": "5"
          }
        }
      }
    }
  },
  "settings": {
    "template_item_settings": [
      {
        "field_name": "items.date",
        "display_preference": {
          "hidden": true
        }
      },
      {
        "field_name": "items.discount",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "items.tax",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "items.description",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "items.quantity",
        "display_preference": {
          "hidden": true
        }
      }
    ],
    "template_subtotal_settings": [
      {
        "field_name": "custom",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "discount",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "shipping",
        "display_preference": {
          "hidden": false
        }
      }
    ]
  },
  "unit_of_measure": "QUANTITY",
  "standard_template": false
}', true);
    }

    private function mockUpdateInvoiceTemplateParams(): array
    {
        return Utils::jsonDecode('{
  "default_template": true,
  "template_info": {
    "configuration": {
      "tax_calculated_after_discount": true,
      "tax_inclusive": false,
      "allow_tip": true,
      "partial_payment": {
        "allow_partial_payment": true,
        "minimum_amount_due": {
          "currency_code": "USD",
          "value": "20.00"
        }
      }
    },
    "detail": {
      "reference": "deal-reference-value",
      "note": "Thank you for your business.",
      "currency_code": "USD",
      "terms_and_conditions": "No refunds after 30 days.",
      "memo": "This is a long contract",
      "attachments": [
        {
          "id": "Screen Shot 2018-11-23 at 16.45.01.png",
          "reference_url": "https://example.com/invoice/payerView/attachments/RkG9ggQbd4Mwm1tYdcF6uuixfFTFq32bBdbE1VbtQLdKSoS2ZOYpfjw9gPp7eTrZmVaFaDWzixHXm-OXWHbmigHigHzURDxJs8IIKqcqP8jawnBEZcraEAPVMULxf5iTyOSpAUc2ugW0PWdwDbM6mg-guFAUyj3Z98H7htWNjQY95jb9heOlcSXUe.sbDUR9smAszzzJoA1NXT6rEEegwQ&version=1&sig=JNODB0xEayW8txMQm6ZsIwDnd4eh3hd6ijiRLi4ipHE"
        }
      ],
      "payment_term": {
        "term_type": "NET_10"
      }
    },
    "invoicer": {
      "name": {
        "given_name": "David",
        "surname": "Larusso"
      },
      "address": {
        "address_line_1": "1234 First Street",
        "address_line_2": "337673 Hillside Court",
        "admin_area_2": "Anytown",
        "admin_area_1": "CA",
        "postal_code": "98765",
        "country_code": "US"
      },
      "email_address": "merchant@example.com",
      "phones": [
        {
          "country_code": "001",
          "national_number": "4085551234",
          "phone_type": "MOBILE"
        }
      ],
      "website": "www.test.com",
      "tax_id": "ABcNkWSfb5ICTt73nD3QON1fnnpgNKBy-Jb5SeuGj185MNNw6g",
      "logo_url": "https://example.com/logo.PNG",
      "additional_notes": "2-4"
    },
    "primary_recipients": [
      {
        "billing_info": {
          "name": {
            "given_name": "Stephanie",
            "surname": "Meyers"
          },
          "address": {
            "address_line_1": "1234 Main Street",
            "admin_area_2": "Anytown",
            "admin_area_1": "CA",
            "postal_code": "98765",
            "country_code": "US"
          },
          "email_address": "bill-me@example.com",
          "phones": [
            {
              "country_code": "001",
              "national_number": "4884551234",
              "phone_type": "MOBILE"
            }
          ],
          "additional_info": "add-info"
        },
        "shipping_info": {
          "name": {
            "given_name": "Stephanie",
            "surname": "Meyers"
          },
          "address": {
            "address_line_1": "1234 Main Street",
            "admin_area_2": "Anytown",
            "admin_area_1": "CA",
            "postal_code": "98765",
            "country_code": "US"
          }
        }
      }
    ],
    "additional_recipients": [
      "inform-me@example.com"
    ],
    "items": [
      {
        "name": "Yoga Mat",
        "description": "new watch",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "50.00"
        },
        "tax": {
          "name": "Sales Tax",
          "percent": "7.25"
        },
        "discount": {
          "percent": "5"
        },
        "unit_of_measure": "QUANTITY"
      },
      {
        "name": "Yoga T Shirt",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "10.00"
        },
        "tax": {
          "name": "Sales Tax",
          "percent": "7.25"
        },
        "discount": {
          "amount": {
            "currency_code": "USD",
            "value": "5.00"
          }
        },
        "unit_of_measure": "QUANTITY"
      }
    ],
    "amount": {
      "currency_code": "USD",
      "value": "74.21",
      "breakdown": {
        "custom": {
          "label": "Packing Charges",
          "amount": {
            "currency_code": "USD",
            "value": "10.00"
          }
        },
        "shipping": {
          "amount": {
            "currency_code": "USD",
            "value": "10.00"
          },
          "tax": {
            "name": "Sales Tax",
            "percent": "7.25"
          }
        },
        "discount": {
          "invoice_discount": {
            "percent": "5"
          }
        }
      }
    }
  },
  "settings": {
    "template_item_settings": [
      {
        "field_name": "items.date",
        "display_preference": {
          "hidden": true
        }
      },
      {
        "field_name": "items.discount",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "items.tax",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "items.description",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "items.quantity",
        "display_preference": {
          "hidden": true
        }
      }
    ],
    "template_subtotal_settings": [
      {
        "field_name": "custom",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "discount",
        "display_preference": {
          "hidden": false
        }
      },
      {
        "field_name": "shipping",
        "display_preference": {
          "hidden": false
        }
      }
    ]
  },
  "unit_of_measure": "QUANTITY",
  "standard_template": false
}', true);
    }
}
