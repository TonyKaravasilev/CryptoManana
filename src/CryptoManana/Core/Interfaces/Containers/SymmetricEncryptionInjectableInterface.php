<?php

/**
 * Interface for dependency containers using the setter dependency injection type for symmetric algorithm services.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractBlockCipherAlgorithm as SymmetricBlockCipher;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;

/**
 * Interface SymmetricEncryptionInjectableInterface - Interface specification for injection via a setter method.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface SymmetricEncryptionInjectableInterface
{
    /**
     * Setter for the message symmetric encryption service.
     *
     * @param SymmetricBlockCipher|DataEncryption $cipher The message encryption service.
     */
    public function setSymmetricCipher(SymmetricBlockCipher $cipher);

    /**
     * Getter for the message symmetric encryption service.
     *
     * @return SymmetricBlockCipher|DataEncryption|null The currently injected message encryption service or null.
     */
    public function getSymmetricCipher();
}
