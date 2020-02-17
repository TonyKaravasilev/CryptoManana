<?php

/**
 * Trait implementation of the setter dependency injection type for symmetric algorithm services.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipher;
use \CryptoManana\Core\Interfaces\Containers\SymmetricEncryptionInjectableInterface as SymmetricCipherSpecification;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;

/**
 * Trait SymmetricEncryptionInjectableTrait - Reusable implementation of `SymmetricEncryptionInjectableInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\SymmetricEncryptionInjectableInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property SymmetricBlockCipher|DataEncryption|null $symmetricCipherSource The message symmetric encryption service.
 *
 * @mixin SymmetricCipherSpecification
 */
trait SymmetricEncryptionInjectableTrait
{
    /**
     * Setter for the message symmetric encryption service.
     *
     * @param SymmetricBlockCipher|DataEncryption $cipher The message symmetric encryption service.
     *
     * @return $this The container object.
     */
    public function setSymmetricCipher(SymmetricBlockCipher $cipher)
    {
        if ($cipher instanceof DataEncryption) {
            $this->symmetricCipherSource = $cipher;
        }

        return $this;
    }

    /**
     * Getter for the message symmetric encryption service.
     *
     * @return SymmetricBlockCipher|DataEncryption|null The currently injected message encryption service or null.
     */
    public function getSymmetricCipher()
    {
        return $this->symmetricCipherSource;
    }
}
