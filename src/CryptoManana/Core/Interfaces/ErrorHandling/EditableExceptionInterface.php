<?php

/**
 * Interface allowing native exceptions to have public property setter methods.
 */

namespace CryptoManana\Core\Interfaces\ErrorHandling;

/**
 * Interface EditableExceptionInterface - Interface allowing exceptions to have property setter methods.
 *
 * @package CryptoManana\Core\Interfaces\ErrorHandling
 */
interface EditableExceptionInterface
{
    /**
     * Change the exception's error message via a fluent interface call.
     *
     * @param string|mixed $message Set a different error message.
     *
     * @return $this The exception object.
     */
    public function setMessage($message);

    /**
     * Change the exception's error code via a fluent interface call.
     *
     * @param int $code Set a different exception error code.
     *
     * @return $this The exception object.
     */
    public function setCode($code);

    /**
     * Change the file location where the exception occurred via a fluent interface call.
     *
     * @param string|mixed $file Set a different file path for the exception.
     *
     * @return $this The exception object.
     */
    public function setFile($file);

    /**
     * Change the file location where the exception occurred via a fluent interface call.
     *
     * @param int $line Set a different file line for the exception.
     *
     * @return $this The exception object.
     */
    public function setLine($line);
}
