<?php

namespace Srmklive\PayPal\Traits\PayPalAPI\InvoiceSearch;

use Srmklive\PayPal\Services\PayPal;
use Carbon\Carbon;

trait Filters
{
    /**
     * @var array<string, mixed>
     */
    protected $invoice_search_filters = [];

    /**
     * @var list<string>
     */
    protected $invoices_date_types = [
        'invoice_date',
        'due_date',
        'payment_date',
        'creation_date',
    ];

    /**
     * @var list<string>
     */
    protected $invoices_status_types = [
        'DRAFT',
        'SENT',
        'SCHEDULED',
        'PAID',
        'MARKED_AS_PAID',
        'CANCELLED',
        'REFUNDED',
        'PARTIALLY_PAID',
        'PARTIALLY_REFUNDED',
        'MARKED_AS_REFUNDED',
        'UNPAID',
        'PAYMENT_PENDING',
    ];

    public function addInvoiceFilterByRecipientEmail(string $email): PayPal
    {
        $this->invoice_search_filters['recipient_email'] = $email;

        return $this;
    }

    public function addInvoiceFilterByRecipientFirstName(string $name): PayPal
    {
        $this->invoice_search_filters['recipient_first_name'] = $name;

        return $this;
    }

    public function addInvoiceFilterByRecipientLastName(string $name): PayPal
    {
        $this->invoice_search_filters['recipient_last_name'] = $name;

        return $this;
    }

    public function addInvoiceFilterByRecipientBusinessName(string $name): PayPal
    {
        $this->invoice_search_filters['recipient_business_name'] = $name;

        return $this;
    }

    public function addInvoiceFilterByInvoiceNumber(string $invoice_number): PayPal
    {
        $this->invoice_search_filters['invoice_number'] = $invoice_number;

        return $this;
    }

    /**
     * @param list<string> $status
     *
     * @throws \Exception
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#definition-invoice_status
     */
    public function addInvoiceFilterByInvoiceStatus(array $status): PayPal
    {
        $invalid_status = false;

        foreach ($status as $item) {
            if (! in_array($item, $this->invoices_status_types)) {
                $invalid_status = true;
                break;
            }
        }

        if ($invalid_status === true) {
            throw new \Exception('status should be always one of these: '.implode(',', $this->invoices_status_types));
        }

        $this->invoice_search_filters['status'] = $status;

        return $this;
    }

    public function addInvoiceFilterByReferenceorMemo(string $reference, bool $memo = false): PayPal
    {
        $field = ($memo === false) ? 'reference' : 'memo';

        $this->invoice_search_filters[$field] = $reference;

        return $this;
    }

    public function addInvoiceFilterByCurrencyCode(string $currency_code = ''): PayPal
    {
        $currency = empty($currency_code) ? $this->getCurrency() : $currency_code;

        $this->invoice_search_filters['currency_code'] = $currency;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function addInvoiceFilterByAmountRange(float $start_amount, float $end_amount, string $amount_currency = ''): PayPal
    {
        if ($start_amount > $end_amount) {
            throw new \Exception('Starting amount should always be less than end amount!');
        }

        $currency = empty($amount_currency) ? $this->getCurrency() : $amount_currency;

        $this->invoice_search_filters['total_amount_range'] = [
            'lower_amount' => [
                'currency_code' => $currency,
                'value' => number_format($start_amount, 2, '.', ''),
            ],
            'upper_amount' => [
                'currency_code' => $currency,
                'value' => number_format($end_amount, 2, '.', ''),
            ],
        ];

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function addInvoiceFilterByDateRange(string $start_date, string $end_date, string $date_type): PayPal
    {
        $start_date_obj = Carbon::parse($start_date);
        $end_date_obj = Carbon::parse($end_date);

        if ($start_date_obj->gt($end_date_obj)) {
            throw new \Exception('Starting date should always be less than the end date!');
        }

        if (! in_array($date_type, $this->invoices_date_types)) {
            throw new \Exception('date type should be always one of these: '.implode(',', $this->invoices_date_types));
        }

        $this->invoice_search_filters["{$date_type}_range"] = [
            'start' => $start_date_obj->toDateString(),
            'end' => $end_date_obj->toDateString(),
        ];

        return $this;
    }

    public function addInvoiceFilterByArchivedStatus(?bool $archived = null): PayPal
    {
        $this->invoice_search_filters['archived'] = $archived;

        return $this;
    }

    /**
     * @param list<string> $fields
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#definition-field
     */
    public function addInvoiceFilterByFields(array $fields): PayPal
    {
        $this->invoice_search_filters['fields'] = $fields;

        return $this;
    }
}
