<?php

/**
 * The framework exception for marking wrong algorithm/system configuration errors.
 */

namespace CryptoManana\Exceptions;

use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractAlgorithmException as FrameworkAlgorithmException;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class WrongConfigurationException - The framework exception for marking wrong algorithm/system configuration errors.
 *
 * @package CryptoManana\Exceptions
 */
class WrongConfigurationException extends FrameworkAlgorithmException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 14;

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
