<?php

/**
 * The framework exception for marking identification logical errors.
 */

namespace CryptoManana\Exceptions;

use CryptoManana\Core\Abstractions\ErrorHandling\AbstractIdentificationException as FrameworkIdentificationException;
use CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class IdentificationFailureException - The framework exception for marking identification errors.
 *
 * @package CryptoManana\Exceptions
 */
class IdentificationFailureException extends FrameworkIdentificationException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 9;

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
