<?php

/**
 * Interface for specifying data encryption/decryption for asymmetric/symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface DataEncryptionInterface - Interface for data encryption/decryption.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface DataEncryptionInterface
{
    /**
     * Encrypts the given plain data.
     *
     * @param string $plainData The plain input string.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     */
    public function encryptData($plainData);

    /**
     * Decrypts the given cipher data.
     *
     * @param string $cipherData The encrypted input string.
     *
     * @return string The decrypted/plain data.
     * @throws \Exception Validation errors.
     */
    public function decryptData($cipherData);
}
