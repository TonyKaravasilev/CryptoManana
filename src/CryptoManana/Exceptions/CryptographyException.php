<?php

/**
 * The framework exception for marking cryptography logical errors.
 */

namespace CryptoManana\Exceptions;

use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class CryptographyException - The framework exception for marking cryptography errors.
 *
 * @package CryptoManana\Exceptions
 */
class CryptographyException extends FrameworkException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 1;

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
     * @see CryptographyException::INTERNAL_CODE Default error code.
     *
     */
    public function getFrameworkErrorCode()
    {
        return static::INTERNAL_CODE;
    }
}
