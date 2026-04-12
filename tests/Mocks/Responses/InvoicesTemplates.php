<?php

namespace Srmklive\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait InvoicesTemplates
{
    private function mockCreateInvoiceTemplateResponse(): array
    {
        return Utils::jsonDecode('{
  "id": "TEMP-19V05281TU309413B",
  "name": "reference-temp",
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
      },
      "metadata": {
        "create_time": "2018-12-03T03:38:46z"
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
        "id": "ITEM-9R873787D1610780X",
        "name": "Yoga Mat",
        "description": "new watch",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "50.00"
        },
        "tax": {
          "id": "TAX-9R873787D1610780X",
          "name": "Sales Tax",
          "percent": "7.25",
          "amount": {
            "currency_code": "USD",
            "value": "3.27"
          }
        },
        "discount": {
          "percent": "5",
          "amount": {
            "currency_code": "USD",
            "value": "2.5"
          }
        },
        "unit_of_measure": "QUANTITY"
      },
      {
        "id": "ITEM-4XD34145EH4061035",
        "name": "Yoga T Shirt",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "10.00"
        },
        "tax": {
          "id": "TAX-4XD34145EH4061035",
          "name": "Sales Tax",
          "percent": "7.25",
          "amount": {
            "currency_code": "USD",
            "value": "0.34"
          }
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
        "item_total": {
          "currency_code": "USD",
          "value": "60.00"
        },
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
            "percent": "7.25",
            "amount": {
              "currency_code": "USD",
              "value": "0.73"
            }
          }
        },
        "discount": {
          "item_discount": {
            "currency_code": "USD",
            "value": "-7.50"
          },
          "invoice_discount": {
            "percent": "5",
            "amount": {
              "currency_code": "USD",
              "value": "-2.63"
            }
          }
        },
        "tax_total": {
          "currency_code": "USD",
          "value": "4.34"
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
  "standard_template": false,
  "links": [
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "delete",
      "method": "DELETE"
    },
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "replace",
      "method": "PUT"
    }
  ]
}', true);
    }

    /**
     * @return array
     */
    private function mockListInvoiceTemplateResponse()
    {
        return Utils::jsonDecode('{
  "addresses": [
    {
      "address_line_1": "1234 First Street",
      "address_line_2": "337673 Hillside Court",
      "admin_area_2": "Anytown",
      "admin_area_1": "CA",
      "postal_code": "98765",
      "country_code": "US"
    },
    {
      "address_line_1": "26303 E 8216 N",
      "address_line_2": "045608 Ocean Bay Plaza #02",
      "admin_area_2": "Garden City",
      "admin_area_1": "NY",
      "postal_code": "11530",
      "country_code": "US"
    }
  ],
  "emails": "email@example.com, email2@example.com",
  "phones": [
    {
      "country_code": "001",
      "national_number": "4085551234",
      "phone_type": "HOME"
    },
    {
      "country_code": "1",
      "national_number": "3477832250",
      "phone_type": "MOBILE"
    },
    {
      "country_code": "1",
      "national_number": "3479543267",
      "phone_type": "FAX"
    },
    {
      "country_code": "1",
      "national_number": "7183514942",
      "phone_type": "OTHER"
    }
  ],
  "templates": [
    {
      "id": "TEMP-19V05281TU309413B",
      "name": "reference-temp",
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
          "currency_code": "USD",
          "note": "Thank you for your business.",
          "terms_and_conditions": "No refunds after 30 days.",
          "memo": "This is a long contract",
          "attachments": [
            {
              "id": "Screen Shot 2018-11-23 at 16.45.01.png",
              "reference_url": "https://exxample.com/invoice/payerView/attachments/RkG9ggQbd4Mwm1tYdcF6uuixfFTFq32bBdbE1VbtQLdKSoS2ZOYpfjw9gPp7eTrZmVaFaDWzixHXm-OXWHbmigHigHzURDxJs8IIKqcqP8jawnBEZcraEAPVMULxf5iTyOSpAUc2ugW0PWdwDbM6mg-guFAUyj3Z98H7htWNjQY95jb9heOlcSXUe.sbDUR9smAszzzJoA1NXT6rEEegwQ&version=1&sig=JNODB0xEayW8txMQm6ZsIwDnd4eh3hd6ijiRLi4ipHE"
            }
          ],
          "payment_term": {
            "term_type": "NET_10"
          },
          "metadata": {
            "create_time": "2018-12-03T03:38:46z"
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
            "id": "ITEM-9R873787D1610780X",
            "name": "Yoga Mat",
            "description": "new watch",
            "quantity": "1",
            "unit_amount": {
              "currency_code": "USD",
              "value": "50.00"
            },
            "tax": {
              "id": "TAX-9R873787D1610780X",
              "name": "Sales Tax",
              "percent": "7.25",
              "amount": {
                "currency_code": "USD",
                "value": "3.27"
              }
            },
            "discount": {
              "percent": "5",
              "amount": {
                "currency_code": "USD",
                "value": "2.5"
              }
            },
            "unit_of_measure": "QUANTITY"
          },
          {
            "id": "ITEM-4XD34145EH4061035",
            "name": "Yoga t-shirt",
            "quantity": "1",
            "unit_amount": {
              "currency_code": "USD",
              "value": "10.00"
            },
            "tax": {
              "id": "TAX-4XD34145EH4061035",
              "name": "Sales Tax",
              "percent": "7.25",
              "amount": {
                "currency_code": "USD",
                "value": "0.34"
              }
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
            "item_total": {
              "currency_code": "USD",
              "value": "60.00"
            },
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
                "percent": "7.25",
                "amount": {
                  "currency_code": "USD",
                  "value": "0.73"
                }
              }
            },
            "discount": {
              "item_discount": {
                "currency_code": "USD",
                "value": "-7.50"
              },
              "invoice_discount": {
                "percent": "5",
                "amount": {
                  "currency_code": "USD",
                  "value": "-2.63"
                }
              }
            },
            "tax_total": {
              "currency_code": "USD",
              "value": "4.34"
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
      "standard_template": false,
      "links": [
        {
          "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
          "rel": "self",
          "method": "GET"
        }
      ]
    },
    {
      "default_template": true,
      "id": "TEMP-11E67842VH3080617",
      "name": "Quantity",
      "template_info": {
        "invoicer": {
          "name": {
            "given_name": "David",
            "surname": "Larusso"
          },
          "email_address": "bill-me@example.com"
        },
        "detail": {
          "currency_code": "USD"
        }
      },
      "standard_template": false,
      "links": [
        {
          "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-11E67842VH3080617",
          "rel": "self",
          "method": "GET"
        }
      ]
    },
    {
      "default_template": false,
      "id": "TEMP-6HC14139B8663074X",
      "name": "Hours",
      "template_info": {
        "invoicer": {
          "name": {
            "given_name": "David",
            "surname": "Larusso"
          },
          "email_address": "bill-me@example.com"
        },
        "detail": {
          "currency_code": "USD"
        }
      },
      "standard_template": false,
      "links": [
        {
          "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-6HC14139B8663074X",
          "rel": "self",
          "method": "GET"
        }
      ]
    }
  ]
}', true);
    }

    /**
     * @return array
     */
    private function mockUpdateInvoiceTemplateResponse()
    {
        return Utils::jsonDecode('{
  "id": "TEMP-19V05281TU309413B",
  "name": "reference-temp",
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
      "currency_code": "USD",
      "note": "Thank you for your business.",
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
      },
      "metadata": {
        "create_time": "2018-12-03T03:38:46z"
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
        "id": "ITEM-9R873787D1610780X",
        "name": "Yoga Mat",
        "description": "new watch",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "50.00"
        },
        "tax": {
          "id": "TAX-9R873787D1610780X",
          "name": "Sales Tax",
          "percent": "7.25",
          "amount": {
            "currency_code": "USD",
            "value": "3.27"
          }
        },
        "discount": {
          "percent": "5",
          "amount": {
            "currency_code": "USD",
            "value": "2.5"
          }
        },
        "unit_of_measure": "QUANTITY"
      },
      {
        "id": "ITEM-4XD34145EH4061035",
        "name": "Yoga T Shirt",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "10.00"
        },
        "tax": {
          "id": "TAX-4XD34145EH4061035",
          "name": "Sales Tax",
          "percent": "7.25",
          "amount": {
            "currency_code": "USD",
            "value": "0.34"
          }
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
        "item_total": {
          "currency_code": "USD",
          "value": "60.00"
        },
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
            "percent": "7.25",
            "amount": {
              "currency_code": "USD",
              "value": "0.73"
            }
          }
        },
        "discount": {
          "item_discount": {
            "currency_code": "USD",
            "value": "-7.50"
          },
          "invoice_discount": {
            "percent": "5",
            "amount": {
              "currency_code": "USD",
              "value": "-2.63"
            }
          }
        },
        "tax_total": {
          "currency_code": "USD",
          "value": "4.34"
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
  "standard_template": false,
  "links": [
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "delete",
      "method": "DELETE"
    },
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "replace",
      "method": "PUT"
    }
  ]
}', true);
    }

    /**
     * @return array
     */
    private function mockGetInvoiceTemplateResponse()
    {
        return Utils::jsonDecode('{
  "id": "TEMP-19V05281TU309413B",
  "name": "reference-temp",
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
      "currency_code": "USD",
      "note": "Thank you for your business.",
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
      },
      "metadata": {
        "create_time": "2018-12-03T03:38:46z"
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
        "id": "ITEM-9R873787D1610780X",
        "name": "Yoga Mat",
        "description": "new watch",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "50.00"
        },
        "tax": {
          "id": "TAX-9R873787D1610780X",
          "name": "Sales Tax",
          "percent": "7.25",
          "amount": {
            "currency_code": "USD",
            "value": "3.27"
          }
        },
        "discount": {
          "percent": "5",
          "amount": {
            "currency_code": "USD",
            "value": "2.5"
          }
        },
        "unit_of_measure": "QUANTITY"
      },
      {
        "id": "ITEM-4XD34145EH4061035",
        "name": "Yoga T Shirt",
        "quantity": "1",
        "unit_amount": {
          "currency_code": "USD",
          "value": "10.00"
        },
        "tax": {
          "id": "TAX-4XD34145EH4061035",
          "name": "Sales Tax",
          "percent": "7.25",
          "amount": {
            "currency_code": "USD",
            "value": "0.34"
          }
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
        "item_total": {
          "currency_code": "USD",
          "value": "60.00"
        },
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
            "percent": "7.25",
            "amount": {
              "currency_code": "USD",
              "value": "0.73"
            }
          }
        },
        "discount": {
          "item_discount": {
            "currency_code": "USD",
            "value": "-7.50"
          },
          "invoice_discount": {
            "percent": "5",
            "amount": {
              "currency_code": "USD",
              "value": "-2.63"
            }
          }
        },
        "tax_total": {
          "currency_code": "USD",
          "value": "4.34"
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
  "standard_template": false,
  "links": [
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "delete",
      "method": "DELETE"
    },
    {
      "href": "https://api.paypal.com/v2/invoicing/templates/TEMP-19V05281TU309413B",
      "rel": "replace",
      "method": "PUT"
    }
  ]
}', true);
    }
}
