<?php

/**
 * Trait implementation of the setter dependency injection type for signature standard services.
 */

namespace CryptoManana\Core\Traits\Containers;

use CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricCipher;
use CryptoManana\Core\Interfaces\Containers\SignatureStandardInjectableInterface as SignatureStandardSpecification;
use CryptoManana\Core\Interfaces\MessageEncryption\DataSigningInterface as DataSigning;

/**
 * Trait SignatureStandardInjectableTrait - Reusable implementation of `SignatureStandardInjectableInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\SignatureStandardInjectableInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property AsymmetricCipher|DataSigning|null $signatureSource The digital signature service.
 *
 * @mixin SignatureStandardSpecification
 */
trait SignatureStandardInjectableTrait
{
    /**
     * Setter for the digital signature service.
     *
     * @param AsymmetricCipher|DataSigning $standard The digital signature service or null.
     *
     * @return $this The container object.
     */
    public function setSignatureStandard(AsymmetricCipher $standard)
    {
        if ($standard instanceof DataSigning) {
            $this->signatureSource = $standard;
        }

        return $this;
    }

    /**
     * Getter for the digital signature service.
     *
     * @return AsymmetricCipher|DataSigning|null $cipher The digital signature service or null.
     */
    public function getSignatureStandard()
    {
        return $this->signatureSource;
    }
}
