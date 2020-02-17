<?php

/**
 * The framework exception for marking expired access token errors.
 */

namespace CryptoManana\Exceptions;

use CryptoManana\Core\Abstractions\ErrorHandling\AbstractAuthenticationException as FrameworkAuthenticationException;
use CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class TokenExpiredException - The framework exception for marking expired access token errors.
 *
 * @package CryptoManana\Exceptions
 */
class TokenExpiredException extends FrameworkAuthenticationException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 13;

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
