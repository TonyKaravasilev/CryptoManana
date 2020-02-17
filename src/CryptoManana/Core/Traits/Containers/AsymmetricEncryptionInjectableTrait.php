<?php

/**
 * Trait implementation of the setter dependency injection type for asymmetric algorithm services.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricEncryption;
use \CryptoManana\Core\Interfaces\Containers\AsymmetricEncryptionInjectableInterface as AsymmetricCipherSpecification;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;

/**
 * Trait AsymmetricEncryptionInjectableTrait - Reusable implementation of `AsymmetricEncryptionInjectableInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\AsymmetricEncryptionInjectableInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property AsymmetricEncryption|DataEncryption|null $asymmetricCipherSource The message asymmetric encryption service.
 *
 * @mixin AsymmetricCipherSpecification
 */
trait AsymmetricEncryptionInjectableTrait
{
    /**
     * Setter for the message asymmetric encryption service.
     *
     * @param AsymmetricEncryption|DataEncryption $cipher The message asymmetric encryption service.
     *
     * @return $this The container object.
     */
    public function setAsymmetricCipher(AsymmetricEncryption $cipher)
    {
        if ($cipher instanceof DataEncryption) {
            $this->asymmetricCipherSource = $cipher;
        }

        return $this;
    }

    /**
     * Getter for the message asymmetric encryption service.
     *
     * @return AsymmetricEncryption|DataEncryption|null The currently injected message encryption service or null.
     */
    public function getAsymmetricCipher()
    {
        return $this->asymmetricCipherSource;
    }
}
