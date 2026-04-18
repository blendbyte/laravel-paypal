<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Carbon\Carbon;
use Psr\Http\Message\StreamInterface;

trait Reporting
{
    /**
     * List all transactions.
     *
     *
     *
     * @param array<string, mixed> $filters
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/transaction-search/v1/#transactions_get
     */
    public function listTransactions(array $filters, string $fields = 'all')
    {
        $filters_list = empty($filters) ? '' : http_build_query($filters).'&';

        $this->apiEndPoint = "v1/reporting/transactions?{$filters_list}fields={$fields}&page={$this->current_page}&page_size={$this->page_size}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Get details for a specific transaction by its ID.
     *
     * Searches the last $daysBack days (max 31, the PayPal API limit). Returns
     * the first matching entry from transaction_details, or null if not found
     * or if the API returns an error.
     *
     * @return array<string, mixed>|null
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/transaction-search/v1/#transactions_get
     */
    public function getTransactionDetails(string $transactionId, int $daysBack = 31): ?array
    {
        $daysBack = max(1, min($daysBack, 31));

        $response = $this->listTransactions([
            'transaction_id' => $transactionId,
            'start_date'     => Carbon::now()->subDays($daysBack)->toIso8601ZuluString(),
            'end_date'       => Carbon::now()->toIso8601ZuluString(),
        ]);

        if (! is_array($response)) {
            return null;
        }

        $details = $response['transaction_details'] ?? [];

        return is_array($details) && count($details) > 0 ? $details[0] : null;
    }

    /**
     * List transactions within a date range without additional filters.
     *
     * @param  \DateTimeInterface|string  $startDate
     * @param  \DateTimeInterface|string  $endDate
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     */
    public function listTransactionsForDateRange(\DateTimeInterface|string $startDate, \DateTimeInterface|string $endDate, string $fields = 'all'): array|StreamInterface|string
    {
        return $this->listTransactions([
            'start_date' => Carbon::parse($startDate)->toIso8601ZuluString(),
            'end_date'   => Carbon::parse($endDate)->toIso8601ZuluString(),
        ], $fields);
    }

    /**
     * List transactions filtered by transaction event code.
     *
     * Common codes: 'T0006' (express checkout payment), 'T0001' (general payment),
     * 'T1107' (payment refund). See PayPal docs for the full list.
     *
     * @param  \DateTimeInterface|string  $startDate
     * @param  \DateTimeInterface|string  $endDate
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/transaction-search/transaction-event-codes/
     */
    public function listTransactionsByType(string $transactionType, \DateTimeInterface|string $startDate, \DateTimeInterface|string $endDate): array|StreamInterface|string
    {
        return $this->listTransactions([
            'transaction_type' => $transactionType,
            'start_date'       => Carbon::parse($startDate)->toIso8601ZuluString(),
            'end_date'         => Carbon::parse($endDate)->toIso8601ZuluString(),
        ]);
    }

    /**
     * List transactions filtered by status code.
     *
     * Common codes: 'S' (success), 'V' (reversed/cancelled), 'P' (pending),
     * 'D' (denied), 'F' (partially refunded).
     *
     * @param  \DateTimeInterface|string  $startDate
     * @param  \DateTimeInterface|string  $endDate
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/transaction-search/transaction-statuses/
     */
    public function listTransactionsByStatus(string $transactionStatus, \DateTimeInterface|string $startDate, \DateTimeInterface|string $endDate): array|StreamInterface|string
    {
        return $this->listTransactions([
            'transaction_status' => $transactionStatus,
            'start_date'         => Carbon::parse($startDate)->toIso8601ZuluString(),
            'end_date'           => Carbon::parse($endDate)->toIso8601ZuluString(),
        ]);
    }

    /**
     * List available balance.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
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
