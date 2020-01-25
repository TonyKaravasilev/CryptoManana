<?php

/**
 * Interface for specifying file encryption/decryption for asymmetric/symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface FileEncryptionInterface - Interface for file encryption/decryption.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface FileEncryptionInterface
{
    /**
     * Encrypts the content of a given plain file.
     *
     * @param string $filename The full path and name of the file for encryption.
     *
     * @return string The encrypted file content.
     * @throws \Exception Validation errors.
     */
    public function encryptFile($filename);

    /**
     * Decrypts the content of a given encrypted file.
     *
     * @param string $filename The full path and name of the file for encryption.
     *
     * @return string The decrypted file content.
     * @throws \Exception Validation errors.
     */
    public function decryptFile($filename);
}
