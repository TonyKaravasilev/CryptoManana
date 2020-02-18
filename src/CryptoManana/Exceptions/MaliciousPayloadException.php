<?php

/**
 * The framework exception for marking malicious payload requests or other injection attempts.
 */

namespace CryptoManana\Exceptions;

use CryptoManana\Core\Abstractions\ErrorHandling\AbstractIdentificationException as FrameworkIdentificationException;
use CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class MaliciousPayloadException - The framework exception for marking malicious payload requests.
 *
 * @package CryptoManana\Exceptions
 */
class MaliciousPayloadException extends FrameworkIdentificationException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 7;

    /**
     * The error code property storage.
     *
     * @see FrameworkException::$code The default error code.
     *
     * @var int The exception's error code.
     */
    protected $code = self::INTERNAL_CODE;

    /**
     * Get the default framework error code for this exception instance.
     *
     * @return int The exception's error code.
     * @see FrameworkException::INTERNAL_CODE Default error code.
     */
    public function getFrameworkErrorCode()
    {
        return static::INTERNAL_CODE; // Correct static access
    }
}
