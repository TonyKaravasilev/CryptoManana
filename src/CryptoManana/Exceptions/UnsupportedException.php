<?php

/**
 * The framework exception for marking unsupported algorithms and features.
 */

namespace CryptoManana\Exceptions;

use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class UnsupportedException - The framework exception for marking unsupported algorithms.
 *
 * @package CryptoManana\Exceptions
 */
class UnsupportedException extends FrameworkException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 3;

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
