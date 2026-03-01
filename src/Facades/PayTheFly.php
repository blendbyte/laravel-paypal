<?php

namespace Srmklive\PayPal\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self setChain(string $chain)
 * @method static string getChain()
 * @method static int getChainId()
 * @method static int getDecimals()
 * @method static string getNativeToken()
 * @method static string getContractAddress()
 * @method static array createPaymentLink(string $serialNo, string $amount, ?string $token = null, ?int $deadline = null, ?string $chain = null)
 * @method static array createBscPayment(string $serialNo, string $amount, ?int $deadline = null)
 * @method static array createTronPayment(string $serialNo, string $amount, ?int $deadline = null)
 * @method static array createTokenPayment(string $serialNo, string $amount, string $tokenAddress, ?string $chain = null, ?int $deadline = null)
 * @method static string signPaymentRequest(string $projectId, string $token, string $amount, string $serialNo, int $deadline, ?string $chain = null)
 * @method static bool verifyWebhookSignature(string $data, string $sign, int $timestamp)
 * @method static array parseWebhook(array $payload)
 * @method static array webhookSuccessResponse()
 * @method static string toSmallestUnit(string $amount, int $decimals)
 * @method static string fromSmallestUnit(string $amount, int $decimals)
 * @method static self setHttpClient(?\GuzzleHttp\Client $client = null)
 *
 * @see \Srmklive\PayPal\Services\PayTheFly
 */
class PayTheFly extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'paythefly_client';
    }
}
