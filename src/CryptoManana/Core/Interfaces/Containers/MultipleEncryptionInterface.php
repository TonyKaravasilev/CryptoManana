<?php

/**
 * Interface for the multiple encryption and decryption of data.
 */

namespace CryptoManana\Core\Interfaces\Containers;

/**
 * Interface MultipleEncryptionInterface - Interface specification for multiple encryption.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface MultipleEncryptionInterface
{
    /**
     * Encrypts the given plain data multiple times with different extracted keys.
     *
     * @param string $plainData The plain input string.
     * @param int $iterations The number of internal iterations to perform.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     */
    public function multipleEncryptData($plainData, $iterations = 2);

    /**
     * Decrypts the given cipher data multiple times with different extracted keys.
     *
     * @param string $cipherData The encrypted input string.
     * @param int $iterations The number of internal iterations to perform.
     *
     * @return string The decrypted/plain data.
     * @throws \Exception Validation errors.
     */
    public function multipleDecryptData($cipherData, $iterations = 2);
}
