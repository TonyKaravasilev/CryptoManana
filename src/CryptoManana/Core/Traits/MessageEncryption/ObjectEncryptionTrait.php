<?php

/**
 * Trait implementation of object encryption/decryption for asymmetric/symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use CryptoManana\Core\Interfaces\MessageEncryption\ObjectEncryptionInterface as ObjectEncryptionSpecification;
use CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryptionSpecification;

/**
 * Trait ObjectEncryptionTrait - Reusable implementation of `ObjectEncryptionInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\ObjectEncryptionInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @mixin ObjectEncryptionSpecification
 * @mixin DataEncryptionSpecification
 */
trait ObjectEncryptionTrait
{
    /**
     * Encrypts the serialized value of the given object.
     *
     * @param object|\stdClass $object The object for encryption.
     *
     * @return string The encrypted serialized object as a string.
     * @throws \Exception Validation errors.
     */
    public function encryptObject($object)
    {
        if (is_object($object)) {
            $object = serialize($object);
        } else {
            throw new \InvalidArgumentException('The data for encryption must be an object instance.');
        }

        return $this->encryptData($object);
    }

    /**
     * Decrypts the encrypted value of the given object.
     *
     * @param string $cipherData The encrypted serialized object as a string.
     *
     * @return object|\stdClass The decrypted and unserialized object.
     * @throws \Exception Validation errors.
     */
    public function decryptObject($cipherData)
    {
        if (!is_string($cipherData)) {
            throw new \InvalidArgumentException('The data for decryption must be a string or a binary string.');
        }

        $object = unserialize($this->decryptData($cipherData));

        if (!is_object($object)) {
            throw new \InvalidArgumentException('The decrypted data must be an object instance.');
        }

        return $object;
    }
}
