<?php

/**
 * Interface for dependency containers using the setter dependency injection type for signature standard services.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use \CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricCipher;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataSigningInterface as DataSigning;

/**
 * Interface SignatureStandardInjectableInterface - Interface specification for injection via a setter method.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface SignatureStandardInjectableInterface
{
    /**
     * Setter for the digital signature service.
     *
     * @param AsymmetricCipher|DataSigning $standard The digital signature service or null.
     */
    public function setSignatureStandard(AsymmetricCipher $standard);

    /**
     * Getter for the digital signature service.
     *
     * @return AsymmetricCipher|DataSigning|null $cipher The digital signature service or null.
     */
    public function getSignatureStandard();
}
