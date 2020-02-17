<?php

/**
 * The framework exception for marking insecure usage cases.
 */

namespace CryptoManana\Exceptions;

use CryptoManana\Core\Abstractions\ErrorHandling\AbstractCryptologyException as FrameworkCryptologyException;
use CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class InsecureUsageException - The framework exception for marking insecure usage cases.
 *
 * @package CryptoManana\Exceptions
 */
class InsecureUsageException extends FrameworkCryptologyException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 15;

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
