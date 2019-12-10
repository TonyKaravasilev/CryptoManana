<?php

/**
 * The framework exception for marking access denied or restricted status.
 */

namespace CryptoManana\Exceptions;

use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class AccessDeniedException - The framework exception for marking access denied per attempt.
 *
 * @package CryptoManana\Exceptions
 */
class AccessDeniedException extends FrameworkException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 5;

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
     * @see UnsupportedException::INTERNAL_CODE Default error code.
     */
    public function getFrameworkErrorCode()
    {
        return static::INTERNAL_CODE; // Correct static access
    }
}
