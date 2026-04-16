<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Srmklive\PayPal\Traits\PayPalAPI\InvoiceSearch\Filters;
use Psr\Http\Message\StreamInterface;

trait InvoicesSearch
{
    use Filters;

    /**
     * Search and return existing invoices.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_list
     */
    public function searchInvoices()
    {
        if (empty($this->invoice_search_filters)) {
            $this->invoice_search_filters = [
                'currency_code' => $this->getCurrency(),
            ];
        }

        $this->apiEndPoint = "v2/invoicing/search-invoices?page={$this->current_page}&page_size={$this->page_size}&total_required={$this->show_totals}";

        $this->options['json'] = $this->invoice_search_filters;

        $this->verb = 'post';

        $response = $this->doPayPalRequest();

        // Reset filters so a subsequent call on the same instance starts fresh.
        $this->invoice_search_filters = [];

        return $response;
    }
}
