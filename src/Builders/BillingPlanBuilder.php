<?php

namespace Srmklive\PayPal\Builders;

use Psr\Http\Message\StreamInterface;
use Srmklive\PayPal\Services\PayPal;

final class BillingPlanBuilder
{
    private const VALID_INTERVALS = ['DAY', 'WEEK', 'MONTH', 'YEAR'];

    private const VALID_FAILURE_ACTIONS = ['CONTINUE', 'CANCEL_SUBSCRIPTION'];

    private string $productId = '';

    private string $name = '';

    private string $description = '';

    private string $currency = 'USD';

    /**
     * @var list<array{tenure_type: string, interval_unit: string, interval_count: int, price: float, total_cycles: int}>
     */
    private array $cycles = [];

    private ?float $setupFeeAmount = null;

    private string $failureAction = 'CONTINUE';

    private int $failureThreshold = 3;

    /** @var array<string, mixed>|null */
    private ?array $taxes = null;

    public static function make(): static
    {
        return new static();
    }

    public function forProduct(string $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function named(string $name, string $description = ''): static
    {
        $this->name = $name;
        $this->description = $description;

        return $this;
    }

    public function withCurrency(string $currency): static
    {
        $this->currency = strtoupper($currency);

        return $this;
    }

    public function withFailureThreshold(int $threshold): static
    {
        $this->failureThreshold = $threshold;

        return $this;
    }

    public function withSetupFee(float $amount, string $failureAction = 'CONTINUE'): static
    {
        if (! in_array($failureAction, self::VALID_FAILURE_ACTIONS, true)) {
            throw new \InvalidArgumentException('Failure action must be one of: '.implode(', ', self::VALID_FAILURE_ACTIONS));
        }

        $this->setupFeeAmount = $amount;
        $this->failureAction = $failureAction;

        return $this;
    }

    public function withTax(float $percentage, bool $inclusive = false): static
    {
        $this->taxes = [
            'percentage' => bcdiv((string) $percentage, '1', 2),
            'inclusive' => $inclusive,
        ];

        return $this;
    }

    // ── Generic cycle adders ──────────────────────────────────────────────────

    public function trialCycle(string $intervalUnit, int $intervalCount, float $price, int $totalCycles = 1): static
    {
        $this->validateInterval($intervalUnit);

        $this->cycles[] = [
            'tenure_type' => 'TRIAL',
            'interval_unit' => strtoupper($intervalUnit),
            'interval_count' => $intervalCount,
            'price' => $price,
            'total_cycles' => $totalCycles,
        ];

        return $this;
    }

    public function regularCycle(string $intervalUnit, int $intervalCount, float $price, int $totalCycles = 0): static
    {
        $this->validateInterval($intervalUnit);

        $this->cycles[] = [
            'tenure_type' => 'REGULAR',
            'interval_unit' => strtoupper($intervalUnit),
            'interval_count' => $intervalCount,
            'price' => $price,
            'total_cycles' => $totalCycles,
        ];

        return $this;
    }

    // ── Regular shortcuts ─────────────────────────────────────────────────────

    public function daily(float $price, int $totalCycles = 0): static
    {
        return $this->regularCycle('DAY', 1, $price, $totalCycles);
    }

    public function weekly(float $price, int $totalCycles = 0): static
    {
        return $this->regularCycle('WEEK', 1, $price, $totalCycles);
    }

    public function monthly(float $price, int $totalCycles = 0): static
    {
        return $this->regularCycle('MONTH', 1, $price, $totalCycles);
    }

    public function annual(float $price, int $totalCycles = 0): static
    {
        return $this->regularCycle('YEAR', 1, $price, $totalCycles);
    }

    // ── Trial shortcuts ───────────────────────────────────────────────────────

    public function trialDaily(float $price, int $totalCycles = 1): static
    {
        return $this->trialCycle('DAY', 1, $price, $totalCycles);
    }

    public function trialWeekly(float $price, int $totalCycles = 1): static
    {
        return $this->trialCycle('WEEK', 1, $price, $totalCycles);
    }

    public function trialMonthly(float $price, int $totalCycles = 1): static
    {
        return $this->trialCycle('MONTH', 1, $price, $totalCycles);
    }

    public function trialAnnual(float $price, int $totalCycles = 1): static
    {
        return $this->trialCycle('YEAR', 1, $price, $totalCycles);
    }

    // ── Terminal methods ──────────────────────────────────────────────────────

    /**
     * Build the plan payload without making an API call.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $billingCycles = [];

        foreach ($this->cycles as $i => $cycle) {
            $billingCycles[] = [
                'frequency' => [
                    'interval_unit' => $cycle['interval_unit'],
                    'interval_count' => $cycle['interval_count'],
                ],
                'tenure_type' => $cycle['tenure_type'],
                'sequence' => $i + 1,
                'total_cycles' => $cycle['total_cycles'],
                'pricing_scheme' => [
                    'fixed_price' => [
                        'value' => bcdiv((string) $cycle['price'], '1', 2),
                        'currency_code' => $this->currency,
                    ],
                ],
            ];
        }

        $paymentPreferences = [
            'auto_bill_outstanding' => true,
            'setup_fee_failure_action' => $this->failureAction,
            'payment_failure_threshold' => $this->failureThreshold,
        ];

        if ($this->setupFeeAmount !== null) {
            $paymentPreferences['setup_fee'] = [
                'value' => bcdiv((string) $this->setupFeeAmount, '1', 2),
                'currency_code' => $this->currency,
            ];
        }

        $plan = [
            'product_id' => $this->productId,
            'name' => $this->name,
            'status' => 'ACTIVE',
            'billing_cycles' => $billingCycles,
            'payment_preferences' => $paymentPreferences,
        ];

        if ($this->description !== '') {
            $plan['description'] = $this->description;
        }

        if ($this->taxes !== null) {
            $plan['taxes'] = $this->taxes;
        }

        return $plan;
    }

    /**
     * Build the payload and call createPlan() on the given provider.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     */
    public function create(PayPal $provider): array|StreamInterface|string
    {
        return $provider->createPlan($this->build());
    }

    private function validateInterval(string $intervalUnit): void
    {
        if (! in_array(strtoupper($intervalUnit), self::VALID_INTERVALS, true)) {
            throw new \InvalidArgumentException(
                'Interval unit must be one of: '.implode(', ', self::VALID_INTERVALS)
            );
        }
    }
}
