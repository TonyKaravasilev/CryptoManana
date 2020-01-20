<?php

/**
 * Interface for specifying secret key capabilities for symmetric encryption algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface SecretKeyInterface - Interface for secret key capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface SecretKeyInterface
{
    /**
     * Setter for the secret key string property.
     *
     * @param string $key The encryption key string.
     *
     * @throws \Exception Validation errors.
     */
    public function setSecretKey($key);

    /**
     * Getter for the secret key string property.
     *
     * @return string The encryption key string.
     */
    public function getSecretKey();
}
