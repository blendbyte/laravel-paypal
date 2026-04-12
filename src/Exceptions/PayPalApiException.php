<?php

namespace Srmklive\PayPal\Exceptions;

use RuntimeException;
use Throwable;

class PayPalApiException extends RuntimeException
{
    /**
     * The raw PayPal error payload — either the decoded JSON object or a
     * plain string message for non-JSON errors (e.g. network failures).
     *
     * @var array<string, mixed>|string
     */
    private array|string $paypalError;

    /**
     * @param array<string, mixed>|string $error
     */
    public function __construct(array|string $error, int $code = 0, ?Throwable $previous = null)
    {
        $this->paypalError = $error;

        $message = is_string($error) ? $error : (string) json_encode($error);

        parent::__construct($message, $code, $previous);
    }

    /**
     * The raw PayPal error payload.
     *
     * For API errors this is typically an array with keys such as
     * `name`, `message`, and `details`. For lower-level failures
     * (e.g. network timeouts) it is a plain string.
     *
     * @return array<string, mixed>|string
     */
    public function getPayPalError(): array|string
    {
        return $this->paypalError;
    }

    /**
     * The HTTP status code of the failed response (e.g. 400, 401, 422, 500).
     *
     * Returns 0 for non-HTTP failures such as network timeouts or
     * connection errors where no response was received.
     */
    public function getHttpStatus(): int
    {
        return $this->getCode();
    }
}
