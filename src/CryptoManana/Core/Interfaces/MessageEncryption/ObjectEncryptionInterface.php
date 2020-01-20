<?php

/**
 * Interface for specifying object encryption/decryption for symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface ObjectEncryptionInterface - Interface for object encryption/decryption.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface ObjectEncryptionInterface
{
    /**
     * Encrypts the serialized value of the given object.
     *
     * @param object|\stdClass $object The object for hashing.
     *
     * @return string The encrypted serialized object as a string.
     * @throws \Exception Validation errors.
     */
    public function encryptObject($object);

    /**
     * Decrypts the encrypted value of the given object.
     *
     * @param string $cipherData The encrypted serialized object as a string.
     *
     * @return object|\stdClass The decrypted and unserialized object.
     * @throws \Exception Validation errors.
     */
    public function decryptObject($cipherData);
}
