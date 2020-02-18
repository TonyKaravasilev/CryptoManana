<?php

/**
 * Interface for dependency containers using the setter dependency injection type for verification algorithm services.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashFunction;
use CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface as VerificationAlgorithm;

/**
 * Interface VerificationAlgorithmInjectableInterface - Interface specification for injection via a setter method.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface VerificationAlgorithmInjectableInterface
{
    /**
     * Setter for the message digestion and verification service.
     *
     * @param HashFunction|VerificationAlgorithm $hasher The message digestion verification service or null.
     */
    public function setVerificationAlgorithm(VerificationAlgorithm $hasher);

    /**
     * Getter for the message digestion and verification service.
     *
     * @return HashFunction|VerificationAlgorithm|null The currently injected message digestion service or null.
     */
    public function getVerificationAlgorithm();
}
