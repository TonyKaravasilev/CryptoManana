<?php

/**
 * Trait implementation of file name and path validation methods.
 */

namespace CryptoManana\Core\Traits\CommonValidations;

use CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait FileNameValidationTrait - Reusable implementation of file name and path validations.
 *
 * @package CryptoManana\Core\Traits\CommonValidations
 */
trait FileNameValidationTrait
{
    /**
     * Internal method for location and filename validation.
     *
     * @param string $filename The filename and location.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateFileNamePath($filename)
    {
        $filename = StringBuilder::stringReplace("\0", '', $filename); // (ASCII 0 (0x00))
        $filename = realpath($filename); // Path traversal escape and absolute path fetching

        // Clear path cache
        if (!empty($filename)) {
            clearstatcache(true, $filename);
        }

        // Check if path is valid and the file is readable
        if ($filename === false || !file_exists($filename) || !is_readable($filename) || !is_file($filename)) {
            throw new \RuntimeException('File is not found or can not be accessed.');
        }
    }
}
