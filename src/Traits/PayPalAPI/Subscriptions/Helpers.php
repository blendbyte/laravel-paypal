<?php

namespace Blendbyte\PayPal\Traits\PayPalAPI\Subscriptions;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

trait Helpers
{
    /**
     * @var array
     */
    protected $trial_pricing = [];

    /**
     * @var int
     */
    protected $payment_failure_threshold = 3;

    /**
     * @var array
     */
    protected $product;

    /**
     * @var array
     */
    protected $billing_plan;

    /**
     * @var array
     */
    protected $shipping_address;

    /**
     * @var array
     */
    protected $payment_preferences;

    /**
     * @var bool
     */
    protected $has_setup_fee = false;

    /**
     * @var array
     */
    protected $taxes;

    /**
     * @var string
     */
    protected $custom_id;

    /**
     * Setup a subscription.
     *
     * @param string $customer_name
     * @param string $customer_email
     * @param string $start_date
     *
     * @throws Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     */
    public function setupSubscription(string $customer_name, string $customer_email, string $start_date = '')
    {
        $body = [
            'plan_id'    => $this->billing_plan['id'],
            'quantity'   => 1,
            'subscriber' => [
                'name'          => [
                    'given_name' => $customer_name,
                ],
                'email_address' => $customer_email,
            ],
        ];

        if (!empty($start_date)) {
            $body['start_time'] = Carbon::parse($start_date)->toIso8601String();
        }

        if ($this->has_setup_fee) {
            $body['plan'] = [
                'payment_preferences' => $this->payment_preferences,
            ];
        }

        if (isset($this->shipping_address)) {
            $body['subscriber']['shipping_address'] = $this->shipping_address;
        }

        if (isset($this->experience_context)) {
            $body['application_context'] = $this->experience_context;
        }

        if (isset($this->taxes)) {
            $body['taxes'] = $this->taxes;
        }

        if (isset($this->custom_id)) {
            $body['custom_id'] = $this->custom_id;
        }

        $subscription = $this->createSubscription($body);
        $subscription['billing_plan_id'] = $this->billing_plan['id'];
        $subscription['product_id'] = $this->product['id'];

        unset($this->product);
        unset($this->billing_plan);
        unset($this->trial_pricing);
        unset($this->return_url);
        unset($this->cancel_url);

        return $subscription;
    }

    /**
     * Add a subscription trial pricing tier.
     *
     * @param string    $interval_type
     * @param int       $interval_count
     * @param float|int $price
     * @param int       $total_cycles
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addPlanTrialPricing(string $interval_type, int $interval_count, float $price = 0, int $total_cycles = 1): \Blendbyte\PayPal\Services\PayPal
    {
        $this->trial_pricing = $this->addPlanBillingCycle($interval_type, $interval_count, $price, $total_cycles, true);

        return $this;
    }

    /**
     * Create a recurring daily billing plan.
     *
     * @param string    $name
     * @param string    $description
     * @param float|int $price
     * @param int       $total_cycles
     *
     * @throws Throwable
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addDailyPlan(string $name, string $description, float $price, int $total_cycles = 0): \Blendbyte\PayPal\Services\PayPal
    {
        if (isset($this->billing_plan)) {
            return $this;
        }

        $plan_pricing = $this->addPlanBillingCycle('DAY', 1, $price, $total_cycles);
        $billing_cycles = empty($this->trial_pricing) ? [$plan_pricing] : collect([$this->trial_pricing, $plan_pricing])->filter()->toArray();

        $this->addBillingPlan($name, $description, $billing_cycles);

        return $this;
    }

    /**
     * Create a recurring weekly billing plan.
     *
     * @param string    $name
     * @param string    $description
     * @param float|int $price
     * @param int       $total_cycles
     *
     * @throws Throwable
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addWeeklyPlan(string $name, string $description, float $price, int $total_cycles = 0): \Blendbyte\PayPal\Services\PayPal
    {
        if (isset($this->billing_plan)) {
            return $this;
        }

        $plan_pricing = $this->addPlanBillingCycle('WEEK', 1, $price, $total_cycles);
        $billing_cycles = empty($this->trial_pricing) ? [$plan_pricing] : collect([$this->trial_pricing, $plan_pricing])->filter()->toArray();

        $this->addBillingPlan($name, $description, $billing_cycles);

        return $this;
    }

    /**
     * Create a recurring monthly billing plan.
     *
     * @param string    $name
     * @param string    $description
     * @param float|int $price
     * @param int       $total_cycles
     *
     * @throws Throwable
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addMonthlyPlan(string $name, string $description, float $price, int $total_cycles = 0): \Blendbyte\PayPal\Services\PayPal
    {
        if (isset($this->billing_plan)) {
            return $this;
        }

        $plan_pricing = $this->addPlanBillingCycle('MONTH', 1, $price, $total_cycles);
        $billing_cycles = empty($this->trial_pricing) ? [$plan_pricing] : collect([$this->trial_pricing, $plan_pricing])->filter()->toArray();

        $this->addBillingPlan($name, $description, $billing_cycles);

        return $this;
    }

    /**
     * Create a recurring annual billing plan.
     *
     * @param string    $name
     * @param string    $description
     * @param float|int $price
     * @param int       $total_cycles
     *
     * @throws Throwable
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addAnnualPlan(string $name, string $description, float $price, int $total_cycles = 0): \Blendbyte\PayPal\Services\PayPal
    {
        if (isset($this->billing_plan)) {
            return $this;
        }

        $plan_pricing = $this->addPlanBillingCycle('YEAR', 1, $price, $total_cycles);
        $billing_cycles = empty($this->trial_pricing) ? [$plan_pricing] : collect([$this->trial_pricing, $plan_pricing])->filter()->toArray();

        $this->addBillingPlan($name, $description, $billing_cycles);

        return $this;
    }

    /**
     * Create a recurring billing plan with custom intervals.
     *
     * @param string    $name
     * @param string    $description
     * @param float|int $price
     * @param string    $interval_unit
     * @param int       $interval_count
     * @param int       $total_cycles
     *
     * @throws Throwable
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addCustomPlan(string $name, string $description, float $price, string $interval_unit, int $interval_count, int $total_cycles = 0): \Blendbyte\PayPal\Services\PayPal
    {
        $billing_intervals = ['DAY', 'WEEK', 'MONTH', 'YEAR'];

        if (isset($this->billing_plan)) {
            return $this;
        }

        if (!in_array($interval_unit, $billing_intervals)) {
            throw new \RuntimeException('Billing intervals should either be '.implode(', ', $billing_intervals));
        }

        $plan_pricing = $this->addPlanBillingCycle($interval_unit, $interval_count, $price, $total_cycles);
        $billing_cycles = empty($this->trial_pricing) ? [$plan_pricing] : collect([$this->trial_pricing, $plan_pricing])->filter()->toArray();

        $this->addBillingPlan($name, $description, $billing_cycles);

        return $this;
    }

    /**
     * Add Plan's Billing cycle.
     *
     * @param string $interval_unit
     * @param int    $interval_count
     * @param float  $price
     * @param int    $total_cycles
     * @param bool   $trial
     *
     * @return array
     */
    protected function addPlanBillingCycle(string $interval_unit, int $interval_count, float $price, int $total_cycles, bool $trial = false): array
    {
        $pricing_scheme = [
            'fixed_price' => [
                'value'         => bcdiv($price, 1, 2),
                'currency_code' => $this->getCurrency(),
            ],
        ];

        if (empty($this->trial_pricing)) {
            $plan_sequence = 1;
        } else {
            $plan_sequence = 2;
        }

        return [
            'frequency'      => [
                'interval_unit'  => $interval_unit,
                'interval_count' => $interval_count,
            ],
            'tenure_type'    => ($trial === true) ? 'TRIAL' : 'REGULAR',
            'sequence'       => ($trial === true) ? 1 : $plan_sequence,
            'total_cycles'   => $total_cycles,
            'pricing_scheme' => $pricing_scheme,
        ];
    }

    /**
     * Create a product for a subscription's billing plan.
     *
     * @param string $name
     * @param string $description
     * @param string $type
     * @param string $category
     *
     * @throws Throwable
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addProduct(string $name, string $description, string $type, string $category): \Blendbyte\PayPal\Services\PayPal
    {
        if (isset($this->product)) {
            return $this;
        }

        $request_id = Str::random();

        $product = $this->createProduct([
            'name'        => $name,
            'description' => $description,
            'type'        => $type,
            'category'    => $category,
        ], $request_id);

        if ($error = data_get($product, 'error', false)) {
            throw new \RuntimeException(data_get($error, 'details.0.description', 'Failed to add product'));
        }
        $this->product = $product;

        return $this;
    }

    /**
     * Add subscription's billing plan's product by ID.
     *
     * @param string $product_id
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addProductById(string $product_id): \Blendbyte\PayPal\Services\PayPal
    {
        $this->product = [
            'id' => $product_id,
        ];

        return $this;
    }

    /**
     * Add subscription's billing plan by ID.
     *
     * @param string $plan_id
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addBillingPlanById(string $plan_id): \Blendbyte\PayPal\Services\PayPal
    {
        $this->billing_plan = [
            'id' => $plan_id,
        ];

        return $this;
    }

    /**
     * Create a product for a subscription's billing plan.
     *
     * @param string $name
     * @param string $description
     * @param array  $billing_cycles
     *
     * @throws Throwable
     *
     * @return void
     */
    protected function addBillingPlan(string $name, string $description, array $billing_cycles): void
    {
        $request_id = Str::random();

        $plan_params = [
            'product_id'          => $this->product['id'],
            'name'                => $name,
            'description'         => $description,
            'status'              => 'ACTIVE',
            'billing_cycles'      => $billing_cycles,
            'payment_preferences' => [
                'auto_bill_outstanding'     => true,
                'setup_fee_failure_action'  => 'CONTINUE',
                'payment_failure_threshold' => $this->payment_failure_threshold,
            ],
        ];

        $billingPlan = $this->createPlan($plan_params, $request_id);
        if ($error = data_get($billingPlan, 'error', false)) {
            throw new \RuntimeException(data_get($error, 'details.0.description', 'Failed to add billing plan'));
        }
        $this->billing_plan = $billingPlan;
    }

    /**
     * Set custom failure threshold when adding a subscription.
     *
     * @param int $threshold
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addPaymentFailureThreshold(int $threshold): \Blendbyte\PayPal\Services\PayPal
    {
        $this->payment_failure_threshold = $threshold;

        return $this;
    }

    /**
     * Add setup fee when adding a subscription.
     *
     * @param float $price
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addSetupFee(float $price): \Blendbyte\PayPal\Services\PayPal
    {
        $this->has_setup_fee = true;
        $this->payment_preferences = [
            'auto_bill_outstanding'     => true,
            'setup_fee'                 => [
                'value'         => bcdiv($price, 1, 2),
                'currency_code' => $this->getCurrency(),
            ],
            'setup_fee_failure_action'  => 'CONTINUE',
            'payment_failure_threshold' => $this->payment_failure_threshold,
        ];

        return $this;
    }

    /**
     * Add shipping address.
     *
     * @param string $full_name
     * @param string $address_line_1
     * @param string $address_line_2
     * @param string $admin_area_2
     * @param string $admin_area_1
     * @param string $postal_code
     * @param string $country_code
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addShippingAddress(string $full_name, string $address_line_1, string $address_line_2, string $admin_area_2, string $admin_area_1, string $postal_code, string $country_code): \Blendbyte\PayPal\Services\PayPal
    {
        $this->shipping_address = [
            'name'    => [
                'full_name' => $full_name,
            ],
            'address' => [
                'address_line_1' => $address_line_1,
                'address_line_2' => $address_line_2,
                'admin_area_2'   => $admin_area_2,
                'admin_area_1'   => $admin_area_1,
                'postal_code'    => $postal_code,
                'country_code'   => $country_code,
            ],
        ];

        return $this;
    }

    /**
     * Add taxes when creating a subscription.
     *
     * @param float $percentage
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addTaxes(float $percentage)
    {
        $this->taxes = [
            'percentage' => $percentage,
            'inclusive'  => false,
        ];

        return $this;
    }

    /**
     * Add custom id.
     *
     * @param string $custom_id
     *
     * @return \Blendbyte\PayPal\Services\PayPal
     */
    public function addCustomId(string $custom_id)
    {
        $this->custom_id = $custom_id;

        return $this;
    }
}
