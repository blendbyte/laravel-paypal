<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI;

use Blendbyte\PayPal\Traits\PayPalAPI\InvoiceSearch\Filters;
use Psr\Http\Message\StreamInterface;

trait InvoicesSearch
{
    use Filters;

    /**
     * Search and return existing invoices.
     *
     *
     * @return array|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_list
     */
    public function searchInvoices()
    {
        if (collect($this->invoice_search_filters)->count() < 1) {
            $this->invoice_search_filters = [
                'currency_code' => $this->getCurrency(),
            ];
        }

        $this->apiEndPoint = "v2/invoicing/search-invoices?page={$this->current_page}&page_size={$this->page_size}&total_required={$this->show_totals}";

        $this->options['json'] = $this->invoice_search_filters;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
