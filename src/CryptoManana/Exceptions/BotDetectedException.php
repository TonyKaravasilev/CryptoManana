<?php

/**
 * The framework exception for marking users that are detected as malicious bots.
 */

namespace CryptoManana\Exceptions;

use \CryptoManana\Core\Abstractions\ErrorHandling\AbstractException as FrameworkException;

/**
 * Class BotDetectedException - The framework exception for marking users as bots.
 *
 * @package CryptoManana\Exceptions
 */
class BotDetectedException extends FrameworkException
{
    /**
     * The framework internal error code.
     */
    const INTERNAL_CODE = 8;

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
     * @see UnsupportedException::INTERNAL_CODE Default error code.
     *
     * @return int The exception's error code.
     */
    public function getFrameworkErrorCode()
    {
        return static::INTERNAL_CODE; // Correct static access
    }
}
