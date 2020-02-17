<?php

/**
 * The framework exception for marking backward incompatible use cases.
 */

namespace CryptoManana\Exceptions;

use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractAlgorithmException as FrameworkAlgorithmException;
use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class IncompatibleException - The framework exception for marking backward incompatible usages.
 *
 * @package CryptoManana\Exceptions
 */
class IncompatibleException extends FrameworkAlgorithmException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 4;

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
