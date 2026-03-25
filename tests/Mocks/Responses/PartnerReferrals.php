<?php

namespace Blendbyte\PayPal\Tests\Mocks\Responses;

use GuzzleHttp\Utils;

trait PartnerReferrals
{
    private function mockCreatePartnerReferralsResponse(): array
    {
        return Utils::jsonDecode('{
  "links": [
    {
      "href": "https://uri.paypal.com/v2/customer/partner-referrals/ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://www.paypal.com/merchantsignup/partner/onboardingentry?token=ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=",
      "rel": "action_url",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockShowReferralDataResponse(): array
    {
        return Utils::jsonDecode('{
  "partner_referral_id": "ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=",
  "submitter_payer_id": "RFYUH2QQDGUQU",
  "referral_data": {
    "individual_owners": [
      {
        "names": [
          {
            "prefix": "Mr.",
            "given_name": "John",
            "surname": "Doe",
            "middle_name": "Middle",
            "suffix": "Jr.",
            "full_name": "John Middle Doe Jr.",
            "type": "LEGAL"
          }
        ],
        "citizenship": "US",
        "addresses": [
          {
            "address_line_1": "One Washington Square",
            "address_line_2": "Apt 123",
            "admin_area_2": "San Jose",
            "admin_area_1": "CA",
            "postal_code": "95112",
            "country_code": "US",
            "type": "HOME"
          }
        ],
        "phones": [
          {
            "country_code": "1",
            "national_number": "6692468839",
            "extension_number": "1234",
            "type": "MOBILE"
          }
        ],
        "birth_details": {
          "date_of_birth": "1955-12-29"
        },
        "type": "PRIMARY"
      }
    ],
    "business_entity": {
      "business_type": {
        "type": "INDIVIDUAL",
        "subtype": "ASSO_TYPE_INCORPORATED"
      },
      "business_industry": {
        "category": "1004",
        "mcc_code": "2025",
        "subcategory": "8931"
      },
      "business_incorporation": {
        "incorporation_country_code": "US",
        "incorporation_date": "1986-12-29"
      },
      "names": [
        {
          "business_name": "Test Enterprise",
          "type": "LEGAL_NAME"
        }
      ],
      "emails": [
        {
          "type": "CUSTOMER_SERVICE",
          "email": "customerservice@example.com"
        }
      ],
      "website": "https://mystore.testenterprises.com",
      "addresses": [
        {
          "address_line_1": "One Washington Square",
          "address_line_2": "Apt 123",
          "admin_area_2": "San Jose",
          "admin_area_1": "CA",
          "postal_code": "95112",
          "country_code": "US",
          "type": "WORK"
        }
      ],
      "phones": [
        {
          "country_code": "1",
          "national_number": "6692478833",
          "extension_number": "1234",
          "type": "CUSTOMER_SERVICE"
        }
      ],
      "beneficial_owners": {
        "individual_beneficial_owners": [
          {
            "names": [
              {
                "prefix": "Mr.",
                "given_name": "John",
                "surname": "Doe",
                "middle_name": "Middle",
                "suffix": "Jr.",
                "full_name": "John Middle Doe Jr.",
                "type": "LEGAL"
              }
            ],
            "citizenship": "US",
            "addresses": [
              {
                "address_line_1": "One Washington Square",
                "address_line_2": "Apt 123",
                "admin_area_2": "San Jose",
                "admin_area_1": "CA",
                "postal_code": "95112",
                "country_code": "US",
                "type": "HOME"
              }
            ],
            "phones": [
              {
                "country_code": "1",
                "national_number": "6692468839",
                "extension_number": "1234",
                "type": "MOBILE"
              }
            ],
            "birth_details": {
              "date_of_birth": "1955-12-29"
            },
            "percentage_of_ownership": "50"
          }
        ],
        "business_beneficial_owners": [
          {
            "business_type": {
              "type": "INDIVIDUAL",
              "subtype": "ASSO_TYPE_INCORPORATED"
            },
            "business_industry": {
              "category": "1004",
              "mcc_code": "2025",
              "subcategory": "8931"
            },
            "business_incorporation": {
              "incorporation_country_code": "US",
              "incorporation_date": "1986-12-29"
            },
            "names": [
              {
                "business_name": "Test Enterprise",
                "type": "LEGAL_NAME"
              }
            ],
            "emails": [
              {
                "type": "CUSTOMER_SERVICE",
                "email": "customerservice@example.com"
              }
            ],
            "website": "https://mystore.testenterprises.com",
            "addresses": [
              {
                "address_line_1": "One Washington Square",
                "address_line_2": "Apt 123",
                "admin_area_2": "San Jose",
                "admin_area_1": "CA",
                "postal_code": "95112",
                "country_code": "US",
                "type": "WORK"
              }
            ],
            "phones": [
              {
                "country_code": "1",
                "national_number": "6692478833",
                "extension_number": "1234",
                "type": "CUSTOMER_SERVICE"
              }
            ],
            "percentage_of_ownership": "50"
          }
        ]
      },
      "office_bearers": [
        {
          "names": [
            {
              "prefix": "Mr.",
              "given_name": "John",
              "surname": "Doe",
              "middle_name": "Middle",
              "suffix": "Jr.",
              "full_name": "John Middle Doe Jr.",
              "type": "LEGAL"
            }
          ],
          "citizenship": "US",
          "addresses": [
            {
              "address_line_1": "One Washington Square",
              "address_line_2": "Apt 123",
              "admin_area_2": "San Jose",
              "admin_area_1": "CA",
              "postal_code": "95112",
              "country_code": "US",
              "type": "HOME"
            }
          ],
          "phones": [
            {
              "country_code": "1",
              "national_number": "6692468839",
              "extension_number": "1234",
              "type": "MOBILE"
            }
          ],
          "birth_details": {
            "date_of_birth": "1955-12-29"
          },
          "role": "DIRECTOR"
        }
      ],
      "annual_sales_volume_range": {
        "minimum_amount": {
          "currency_code": "USD",
          "value": "10000"
        },
        "maximum_amount": {
          "currency_code": "USD",
          "value": "50000"
        }
      },
      "average_monthly_volume_range": {
        "minimum_amount": {
          "currency_code": "USD",
          "value": "1000"
        },
        "maximum_amount": {
          "currency_code": "USD",
          "value": "50000"
        }
      },
      "purpose_code": "P0104"
    },
    "email": "accountemail@example.com",
    "preferred_language_code": "en-US",
    "tracking_id": "testenterprices123122",
    "partner_config_override": {
      "partner_logo_url": "https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg",
      "return_url": "https://testenterprises.com/merchantonboarded",
      "return_url_description": "the url to return the merchant after the paypal onboarding process.",
      "action_renewal_url": "https://testenterprises.com/renew-exprired-url",
      "show_add_credit_card": true
    },
    "operations": [
      {
        "operation": "API_INTEGRATION",
        "api_integration_preference": {
          "classic_api_integration": {
            "integration_type": "THIRD_PARTY",
            "third_party_details": {
              "permissions": [
                "EXPRESS_CHECKOUT",
                "REFUND",
                "DIRECT_PAYMENT",
                "AUTH_CAPTURE",
                "BUTTON_MANAGER",
                "ACCOUNT_BALANCE",
                "TRANSACTION_DETAILS"
              ]
            },
            "first_party_details": "CERTIFICATE"
          },
          "rest_api_integration": {
            "integration_method": "PAYPAL",
            "integration_type": "THIRD_PARTY",
            "third_party_details": {
              "features": [
                "PAYMENT",
                "REFUND",
                "PARTNER_FEE"
              ]
            }
          }
        },
        "billing_agreement": {
          "description": "Billing Agreement Description Field",
          "billing_experience_preference": {
            "experience_id": "string",
            "billing_context_set": true
          },
          "merchant_custom_data": "PatnerMERCHANT23124",
          "approval_url": "wttps://www.paypal.com/agreements/approve?ba_token=BA-67944913LE886121E",
          "ec_token": "EC-1S970363DB536864M"
        }
      }
    ],
    "products": [
      "EXPRESS_CHECKOUT"
    ],
    "legal_consents": [
      {
        "type": "SHARE_DATA_CONSENT",
        "granted": true
      }
    ]
  },
  "links": [
    {
      "href": "https://uri.paypal.com/v2/customer/partner-referrals/ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=",
      "rel": "self",
      "method": "GET"
    },
    {
      "href": "https://www.paypal.com/merchantsignup/partner/onboardingentry?token=ZjcyODU4ZWYtYTA1OC00ODIwLTk2M2EtOTZkZWQ4NmQwYzI3RU12cE5xa0xMRmk1NWxFSVJIT1JlTFdSbElCbFU1Q3lhdGhESzVQcU9iRT0=",
      "rel": "action_url",
      "method": "GET"
    }
  ]
}', true);
    }

    private function mockListSellerTrackingInformationResponse(): array
    {
        return Utils::jsonDecode('{
          "merchant_id": "8LQLM2ML4ZTYU",
          "tracking_id": "merchantref1",
          "links": [
            {
              "href": "v1/customer/partners/6LKMD2ML4NJYU",
              "rel": "read",
              "method": "GET",
              "description": "Main partner resource."
            }
          ]
        }', true);
    }

    private function mockShowSellerStatusResponse(): array
    {
        return Utils::jsonDecode('{
          "merchant_id": "8LQLM2ML4ZTYU",
          "products": [
            {
              "name": "PAYFLOW_PRO",
              "vetting_status": "APPROVED",
              "status": "ACTIVE"
            },
            {
              "name": "EXPRESS_CHECKOUT",
              "status": "ACTIVE"
            },
            {
              "name": "PPCP_STANDARD",
              "vetting_status": "SUBSCRIBED",
              "capabilities": [
                "CUSTOM_CARD_PROCESSING",
                "CARD_PROCESSING_VIRTUAL_TERMINAL",
                "FRAUD_TOOL_ACCESS",
                "AMEX_OPTBLUE",
                "DEBIT_CARD_SWITCH",
                "COMMERCIAL_ENTITY"
              ]
            },
            {
              "name": "PPCP_CUSTOM",
              "vetting_status": "IN_REVIEW",
              "capabilities": []
            }
          ],
          "capabilities": [
            {
              "name": "CUSTOM_CARD_PROCESSING",
              "status": "ACTIVE",
              "limits": [
                {
                  "type": "GENERAL"
                }
              ]
            },
            {
              "name": "CARD_PROCESSING_VIRTUAL_TERMINAL",
              "status": "ACTIVE"
            },
            {
              "name": "FRAUD_TOOL_ACCESS",
              "status": "ACTIVE"
            },
            {
              "name": "AMEX_OPTBLUE",
              "status": "ACTIVE"
            },
            {
              "name": "DEBIT_CARD_SWITCH",
              "status": "ACTIVE"
            },
            {
              "name": "COMMERCIAL_ENTITY",
              "status": "ACTIVE"
            }
          ],
          "payments_receivable": true,
          "primary_email": "seller@example.com",
          "primary_email_confirmed": true,
          "granted_permissions": [
            "AIR_TRAVEL",
            "INVOICING",
            "RECURRING_PAYMENT_REPORT"
          ],
          "api_credential": {
            "signature": {
              "api_user_name": "example_api1.gmail.com",
              "api_password": "7QPZJL5PX2TT94RX",
              "signature": "Ak0kqXY-wqI.w.dfyQrwACtkK4HcMNxGdvADyrIE8QLgZWyIDNJSDlQ1e"
            }
          },
          "oauth_integrations": [
            {
              "integration_type": "OAUTH_THIRD_PARTY",
              "oauth_third_party": [
                {
                  "partner_client_id": "AafBGhBphJ66SHPtbCMTsH1q2HQC2lnf0ER0KWAVSsOqsAtVfnye5Vc8hAOC",
                  "merchant_client_id": "AafBGhBphJ66SHPtbCMTsH1q2HQC2lnf0ER0KWAVSsOqsAtVfnye5Vc8hAOC",
                  "scopes": [
                    "https://uri.paypal.com/services/payments/realtimepayment",
                    "https://uri.paypal.com/services/payments/payment/authcapture",
                    "https://uri.paypal.com/services/payments/refund"
                  ]
                },
                {
                  "partner_client_id": "AafBGhBphJ66SHPtbCMTsH1q2HQC2lnf0ER0KWAVSsOqsAtVfnye5Vc8hAOC",
                  "merchant_client_id": "AafBGhBphJ66SHPtbCMTsH1q2HQC2lnf0ER0KWAVSsOqsAtVfnye5Vc8hAOC",
                  "scopes": [
                    "https://uri.paypal.com/services/payments/realtimepayment",
                    "https://uri.paypal.com/services/payments/payment/authcapture"
                  ]
                }
              ]
            }
          ],
          "limitations": [
            {
              "name": "MRCHT - Pending User Agreement",
              "restrictions": [
                "ACH IN",
                "Withdraw Money",
                "Remove Bank",
                "Refunds to Buyer",
                "Close Account",
                "Send Money",
                "Remove Card"
              ]
            },
            {
              "name": "Seller-linked merchant",
              "restrictions": [
                "ACH IN",
                "Send Money",
                "Refunds to Buyer",
                "Receive/Request Money",
                "Remove Bank",
                "Remove Card",
                "Withdraw Money",
                "Close Account"
              ]
            }
          ]
        }', true);
    }

    private function mockListMerchantCredentialsResponse(): array
    {
        return Utils::jsonDecode('{
          "client_id": "Ab27r3fkrQezHdcPrn2b2SYzPEldXx2dWgv76btVfI-eYF8KRAd2WxXAZyb0ETygSNeHBthzlxjlQ_qw",
          "client_secret": "EAcTvpnDHZf4icl_2MPnt2gRpOxHVtaQJChWU3PrRbYR4uyvUXV6h4DWQjm7XOfdnk_OrEEWdxY2eUG3",
          "payer_id": "QVG98CUNMS2PY"
        }', true);
    }
}
