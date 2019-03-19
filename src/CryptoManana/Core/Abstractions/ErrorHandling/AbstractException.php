<?php

/**
 * Abstraction for all framework exceptions.
 */

namespace CryptoManana\Core\Abstractions\ErrorHandling;

use \CryptoManana\Core\Interfaces\ErrorHandling\InterfaceEditableException as EditableProperties;
use \Exception as PhpException;

/**
 * Class AbstractException - Abstraction for all framework exceptions.
 *
 * @package CryptoManana\Core\Abstractions\ErrorHandling
 */
abstract class AbstractException extends PhpException implements EditableProperties
{
    /**
     * The default framework internal error code.
     */
    const INTERNAL_CODE = 0;

    /**
     * Get the default framework error code for this exception instance.
     *
     * @return int The exception's error code.
     */
    abstract public function getFrameworkErrorCode();

    /**
     * Change the exception's error message via a fluent interface call.
     *
     * @param string $message Set a different error message.
     *
     * @return $this The exception object.
     */
    public function setMessage($message)
    {
        $this->message = is_string($message) ? $message : $this->message;

        return $this;
    }

    /**
     * Change the exception's error code via a fluent interface call.
     *
     * @param int $code Set a different exception error code.
     *
     * @return $this The exception object.
     */
    public function setCode($code)
    {
        $this->code = is_int($code) && $code >= 0 ? $code : $this->code;

        return $this;
    }

    /**
     * Change the file location where the exception occurred via a fluent interface call.
     *
     * @param string $file Set a different file path for the exception.
     *
     * @return $this The exception object.
     */
    public function setFile($file)
    {
        $this->file = is_string($file) ? $file : $this->file;

        return $this;
    }

    /**
     * Change the file location where the exception occurred via a fluent interface call.
     *
     * @param $line $file Set a different file line for the exception.
     *
     * @return $this The exception object.
     */
    public function setLine($line)
    {
        $this->line = is_int($line) && $line >= 1 ? $line : $this->line;

        return $this;
    }
}