<?php

/**
 * PayTheFly Crypto Payment Gateway Configuration
 *
 * @see https://pro.paythefly.com/docs
 */

return [
    /*
    |--------------------------------------------------------------------------
    | PayTheFly Project ID
    |--------------------------------------------------------------------------
    |
    | Your unique project identifier from the PayTheFly dashboard.
    |
    */
    'project_id' => env('PAYTHEFLY_PROJECT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Project Key (for webhook HMAC verification)
    |--------------------------------------------------------------------------
    |
    | Used to verify webhook signature integrity via HMAC-SHA256.
    |
    */
    'project_key' => env('PAYTHEFLY_PROJECT_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Private Key (for EIP-712 signature generation)
    |--------------------------------------------------------------------------
    |
    | The ECDSA private key used to sign EIP-712 typed data for payment links.
    | MUST be stored in .env — never commit to source control.
    |
    */
    'private_key' => env('PAYTHEFLY_PRIVATE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Chain
    |--------------------------------------------------------------------------
    |
    | Supported: "bsc" (chainId 56), "tron" (chainId 728126428)
    |
    */
    'default_chain' => env('PAYTHEFLY_DEFAULT_CHAIN', 'bsc'),

    /*
    |--------------------------------------------------------------------------
    | Contract Addresses (verifyingContract for EIP-712)
    |--------------------------------------------------------------------------
    |
    | The deployed PayTheFlyPro contract address on each chain.
    |
    */
    'contracts' => [
        'bsc'  => env('PAYTHEFLY_CONTRACT_BSC', ''),
        'tron' => env('PAYTHEFLY_CONTRACT_TRON', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Token (native token address per chain)
    |--------------------------------------------------------------------------
    |
    | Use these for native currency payments (BNB on BSC, TRX on TRON).
    | Override per-payment to use ERC-20/TRC-20 tokens.
    |
    */
    'native_tokens' => [
        'bsc'  => '0x0000000000000000000000000000000000000000',
        'tron' => 'T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb',
    ],

    /*
    |--------------------------------------------------------------------------
    | Chain Metadata
    |--------------------------------------------------------------------------
    */
    'chains' => [
        'bsc' => [
            'chain_id' => 56,
            'decimals' => 18,
            'symbol'   => 'BSC',
        ],
        'tron' => [
            'chain_id' => 728126428,
            'decimals' => 6,
            'symbol'   => 'TRON',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Link Base URL
    |--------------------------------------------------------------------------
    */
    'payment_url' => env('PAYTHEFLY_PAYMENT_URL', 'https://pro.paythefly.com/pay'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'path'       => env('PAYTHEFLY_WEBHOOK_PATH', '/paythefly/webhook'),
        'tolerance'  => env('PAYTHEFLY_WEBHOOK_TOLERANCE', 300), // seconds
        'middleware'  => ['api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Deadline (seconds from now)
    |--------------------------------------------------------------------------
    */
    'deadline_ttl' => env('PAYTHEFLY_DEADLINE_TTL', 3600), // 1 hour
];
