<?php

/**
 * The framework exception for marking bad security practices.
 */

namespace CryptoManana\Exceptions;

use CryptoManana\Core\Abstractions\ErrorHandling\AbstractCryptologyException as FrameworkCryptologyException;
use CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class BadPracticeException - The framework exception for marking bad security practices.
 *
 * @package CryptoManana\Exceptions
 */
class BadPracticeException extends FrameworkCryptologyException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 2;

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
        return static::INTERNAL_CODE;
    }
}
