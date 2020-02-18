<?php

/**
 * Trait implementation of file encryption/decryption for asymmetric/symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use CryptoManana\Core\Interfaces\MessageEncryption\FileEncryptionInterface as FileEncryptionSpecification;
use CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryptionSpecification;
use CryptoManana\Core\Traits\CommonValidations\FileNameValidationTrait as ValidateFileNames;

/**
 * Trait FileEncryptionTrait - Reusable implementation of `FileEncryptionInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\FileEncryptionInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @mixin FileEncryptionSpecification
 * @mixin DataEncryptionSpecification
 * @mixin ValidateFileNames
 */
trait FileEncryptionTrait
{
    /**
     * File name and path validations.
     *
     * {@internal Reusable implementation of the common file name validation. }}
     */
    use ValidateFileNames;

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
