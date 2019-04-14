<?php

/**
 * The framework exception for marking security breach attempts and network penetrations.
 */

namespace CryptoManana\Exceptions;

use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class AccessDeniedException - The framework exception for marking security breach attempts.
 *
 * @package CryptoManana\Exceptions
 */
class BreachAttemptException extends FrameworkException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 6;

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
     *
     */
    public function getFrameworkErrorCode()
    {
        return static::INTERNAL_CODE; // Correct static access
    }
}
