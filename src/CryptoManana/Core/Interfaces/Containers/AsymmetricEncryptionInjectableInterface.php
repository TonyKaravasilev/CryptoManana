<?php

/**
 * Interface for dependency containers using the setter dependency injection type for asymmetric algorithm services.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricEncryption;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;

/**
 * Interface AsymmetricEncryptionInjectableInterface - Interface specification for injection via a setter method.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface AsymmetricEncryptionInjectableInterface
{
    /**
     * Setter for the message asymmetric encryption service.
     *
     * @param AsymmetricEncryption|DataEncryption $cipher The message asymmetric encryption service.
     */
    public function setAsymmetricCipher(AsymmetricEncryption $cipher);

    /**
     * Getter for the message asymmetric encryption service.
     *
     * @return AsymmetricEncryption|DataEncryption|null The currently injected message encryption service or null.
     */
    public function getAsymmetricCipher();
}
