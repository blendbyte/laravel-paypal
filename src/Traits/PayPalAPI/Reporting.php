<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Carbon\Carbon;

trait Reporting
{
    /**
     * List all transactions.
     *
     * @param array  $filters
     * @param string $fields
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/transaction-search/v1/#transactions_get
     */
    public function listTransactions(array $filters, string $fields = 'all')
    {
        $filters_list = collect($filters)->isEmpty() ? '' :
            collect($filters)->map(function ($value, $key) {
                return "{$key}={$value}&";
            })->implode('');

        $this->apiEndPoint = "v1/reporting/transactions?{$filters_list}fields={$fields}&page={$this->current_page}&page_size={$this->page_size}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * List available balance.
     *
     * @param string $date
     * @param string $balance_currency
     *
     * @throws \Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/transaction-search/v1/#balances_get
     */
    public function listBalances(string $date = '', string $balance_currency = '')
    {
        $date = empty($date) ? Carbon::now()->toIso8601ZuluString() : Carbon::parse($date)->toIso8601ZuluString();
        $currency = empty($balance_currency) ? $this->getCurrency() : $balance_currency;

        $this->apiEndPoint = "v1/reporting/balances?currency_code={$currency}&as_of_time={$date}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
