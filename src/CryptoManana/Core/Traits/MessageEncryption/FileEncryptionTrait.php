<?php

/**
 * Trait implementation of file encryption/decryption for asymmetric/symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use \CryptoManana\Core\Interfaces\MessageEncryption\FileEncryptionInterface as FileEncryptionSpecification;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryptionSpecification;
use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait FileEncryptionTrait - Reusable implementation of `FileEncryptionInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\FileEncryptionInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @mixin FileEncryptionSpecification
 * @mixin DataEncryptionSpecification
 */
trait FileEncryptionTrait
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

    /**
     * Encrypts the content of a given plain file.
     *
     * @param string $filename The full path and name of the file for encryption.
     *
     * @return string The encrypted file content.
     * @throws \Exception Validation errors.
     */
    public function encryptFile($filename)
    {
        if (!is_string($filename)) {
            throw new \InvalidArgumentException('The file path must be of type string.');
        }

        $this->validateFileNamePath($filename);

        return $this->encryptData(file_get_contents($filename));
    }

    /**
     * Decrypts the content of a given encrypted file.
     *
     * @param string $filename The full path and name of the file for encryption.
     *
     * @return string The decrypted file content.
     * @throws \Exception Validation errors.
     */
    public function decryptFile($filename)
    {
        if (!is_string($filename)) {
            throw new \InvalidArgumentException('The file path must be of type string.');
        }

        $this->validateFileNamePath($filename);

        return $this->decryptData(file_get_contents($filename));
    }
}
