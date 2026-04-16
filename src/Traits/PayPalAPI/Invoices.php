<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait Invoices
{
    /**
     * Create a new draft invoice.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_create
     */
    public function createInvoice(array $data)
    {
        $this->apiEndPoint = 'v2/invoicing/invoices';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Get list of invoices.
     *
     *
     *
     * @param list<string> $fields
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_list
     */
    public function listInvoices(array $fields = [])
    {
        $fields = count($fields) > 0 ? '&fields='.implode(',', $fields) : '';

        $this->apiEndPoint = "v2/invoicing/invoices?page={$this->current_page}&page_size={$this->page_size}&total_required={$this->show_totals}{$fields}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Send an existing invoice.
     *
     *
     *
     * @param array<string, mixed> $recipients
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_send
     */
    public function sendInvoice(string $invoice_id, string $subject = '', string $note = '', bool $send_recipient = true, bool $send_merchant = false, array $recipients = [])
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}/send";

        $this->options['json'] = $this->getInvoiceMessagePayload($subject, $note, $recipients, $send_recipient, $send_merchant);

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Send reminder for an existing invoice.
     *
     *
     *
     * @param array<string, mixed> $recipients
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_remind
     */
    public function sendInvoiceReminder(string $invoice_id, string $subject = '', string $note = '', bool $send_recipient = true, bool $send_merchant = false, array $recipients = [])
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}/remind";

        $this->options['json'] = $this->getInvoiceMessagePayload($subject, $note, $recipients, $send_recipient, $send_merchant);

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Cancel an existing invoice which is already sent.
     *
     *
     *
     * @param array<string, mixed> $recipients
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_cancel
     */
    public function cancelInvoice(string $invoice_id, string $subject = '', string $note = '', bool $send_recipient = true, bool $send_merchant = false, array $recipients = [])
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}/cancel";

        $this->options['json'] = $this->getInvoiceMessagePayload($subject, $note, $recipients, $send_recipient, $send_merchant);

        $this->verb = 'post';

        return $this->doPayPalRequest(false);
    }

    /**
     * Register payment against an existing invoice.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_payments
     */
    public function registerPaymentInvoice(string $invoice_id, string $payment_date, string $payment_method, float $amount, string $payment_note = '', string $payment_id = '')
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}/payments";

        $data = [
            'payment_id' => $payment_id,
            'payment_date' => $payment_date,
            'method' => $payment_method,
            'note' => $payment_note,
            'amount' => [
                'currency_code' => $this->currency,
                'value' => number_format($amount, 2, '.', ''),
            ],
        ];

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Delete payment against an existing invoice.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_payments-delete
     */
    public function deleteExternalPaymentInvoice(string $invoice_id, string $transaction_id)
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}/payments/{$transaction_id}";

        $this->verb = 'delete';

        return $this->doPayPalRequest(false);
    }

    /**
     * Register payment against an existing invoice.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_refunds
     */
    public function refundInvoice(string $invoice_id, string $payment_date, string $payment_method, float $amount)
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}/refunds";

        $data = [
            'refund_date' => $payment_date,
            'method' => $payment_method,
            'amount' => [
                'currency_code' => $this->currency,
                'value' => number_format($amount, 2, '.', ''),
            ],
        ];

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Delete refund against an existing invoice.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_refunds-delete
     */
    public function deleteRefundInvoice(string $invoice_id, string $transaction_id)
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}/refunds/{$transaction_id}";

        $this->verb = 'delete';

        return $this->doPayPalRequest(false);
    }

    /**
     * Generate QR code against an existing invoice.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_generate-qr-code
     */
    public function generateQRCodeInvoice(string $invoice_id, int $width = 100, int $height = 100)
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}/generate-qr-code";

        $this->options['json'] = [
            'width' => $width,
            'height' => $height,
        ];
        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Generate the next invoice number.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_generate-next-invoice-number
     */
    public function generateInvoiceNumber()
    {
        $this->apiEndPoint = 'v2/invoicing/generate-next-invoice-number';

        $this->options['json'] = (object) [];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Show details for an existing invoice.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_get
     */
    public function showInvoiceDetails(string $invoice_id)
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Update an existing invoice.
     *
     *
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_update
     */
    public function updateInvoice(string $invoice_id, array $data)
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}";

        $this->options['json'] = $data;

        $this->verb = 'put';

        return $this->doPayPalRequest();
    }

    /**
     * Delete an invoice.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/invoicing/v2/#invoices_delete
     */
    public function deleteInvoice(string $invoice_id)
    {
        $this->apiEndPoint = "v2/invoicing/invoices/{$invoice_id}";

        $this->verb = 'delete';

        return $this->doPayPalRequest(false);
    }

    /**
     * Get Invoice Message Payload.
     *
     * @param array<string, mixed> $recipients
     *
     * @return array<string, mixed>
     */
    protected function getInvoiceMessagePayload(string $subject, string $note, array $recipients, bool $send_recipient, bool $send_merchant): array
    {
        $data = [
            'subject' => ! empty($subject) ? $subject : '',
            'note' => ! empty($note) ? $note : '',
            'additional_recipients' => count($recipients) > 0 ? $recipients : '',
            'send_to_recipient' => $send_recipient,
            'send_to_invoicer' => $send_merchant,
        ];

        return array_filter($data);
    }
}
