<?php

/**
 * Trait implementation of the secret key capabilities for symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use \CryptoManana\Core\Interfaces\MessageEncryption\SecretKeyInterface as SecretKeySpecification;

/**
 * Trait SecretKeyTrait - Reusable implementation of `SecretKeyInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\SecretKeyInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @property string $key The encryption/decryption secret key property storage.
 *
 * @mixin SecretKeySpecification
 */
trait SecretKeyTrait
{
    /**
     * Setter for the secret key string property.
     *
     * @param string $key The encryption key string.
     *
     * @return $this The encryption algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setSecretKey($key)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException('The secret key must be a string or a binary string.');
        }

        /**
         * {@internal The encryption standard is 8-bit wise (don not use StringBuilder) and utilizes performance. }}
         */
        if (strlen($key) > static::KEY_SIZE) {
            $key = hash_hkdf('sha256', $key, static::KEY_SIZE, 'CryptoMañana', '');
        } elseif (strlen($key) < static::KEY_SIZE) {
            $key = str_pad($key, static::KEY_SIZE, "\x0", STR_PAD_RIGHT);
        }

        $this->key = $key;

        return $this;
    }

    /**
     * Getter for the secret key string property.
     *
     * @return string The encryption key string.
     */
    public function getSecretKey()
    {
        return $this->key;
    }
}
