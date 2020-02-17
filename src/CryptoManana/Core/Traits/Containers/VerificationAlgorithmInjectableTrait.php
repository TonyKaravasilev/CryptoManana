<?php

/**
 * Trait implementation of the setter dependency injection type for verification algorithm services.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashFunction;
use \CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface as VerificationAlgorithm;
use \CryptoManana\Core\Interfaces\Containers\VerificationAlgorithmInjectableInterface as VerificationSpecification;

/**
 * Trait VerificationAlgorithmInjectableTrait - Reusable implementation of `VerificationAlgorithmInjectableInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\VerificationAlgorithmInjectableInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property HashFunction|VerificationAlgorithm|null $verificationSource The message digestion and verification service.
 *
 * @mixin VerificationSpecification
 */
trait VerificationAlgorithmInjectableTrait
{
    /**
     * Setter for the message digestion and verification service.
     *
     * @param HashFunction|VerificationAlgorithm $hasher The message digestion verification service or null.
     *
     * @return $this The container object.
     */
    public function setVerificationAlgorithm(VerificationAlgorithm $hasher)
    {
        $this->verificationSource = $hasher;

        return $this;
    }

    /**
     * Getter for the message digestion and verification service.
     *
     * @return HashFunction|VerificationAlgorithm|null The currently injected message digestion service or null.
     */
    public function getVerificationAlgorithm()
    {
        return $this->verificationSource;
    }
}
