<?php

namespace Srmklive\PayPal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a confirmed payment webhook is received.
 *
 * This event fires only for tx_type=1 (payment) with confirmed=true.
 * Use this for order fulfillment, credit allocation, etc.
 *
 * Usage in EventServiceProvider:
 *
 *     PayTheFlyPaymentConfirmed::class => [
 *         FulfillOrder::class,
 *     ],
 */
class PayTheFlyPaymentConfirmed
{
    use Dispatchable, SerializesModels;

    /**
     * Structured payment data.
     *
     * @var array{
     *     project_id: string,
     *     chain_symbol: string,
     *     tx_hash: string,
     *     wallet: string,
     *     value: string,
     *     fee: string,
     *     serial_no: string,
     *     tx_type: string,
     *     confirmed: bool,
     *     create_at: string
     * }
     */
    public array $payment;

    /**
     * Create a new event instance.
     *
     * @param array $payment
     */
    public function __construct(array $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the order serial number.
     *
     * @return string
     */
    public function getSerialNo(): string
    {
        return $this->payment['serial_no'] ?? '';
    }

    /**
     * Get the transaction hash.
     *
     * @return string
     */
    public function getTxHash(): string
    {
        return $this->payment['tx_hash'] ?? '';
    }

    /**
     * Get the payment value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->payment['value'] ?? '0';
    }

    /**
     * Get the payer's wallet address.
     *
     * @return string
     */
    public function getWallet(): string
    {
        return $this->payment['wallet'] ?? '';
    }

    /**
     * Get the chain symbol (e.g., 'BSC', 'TRON').
     *
     * @return string
     */
    public function getChainSymbol(): string
    {
        return $this->payment['chain_symbol'] ?? '';
    }
}
