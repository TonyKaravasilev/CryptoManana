<?php

/**
 * Trait implementation of the password and data verification process for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface as VerificationSpecification;
use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as AnyDerivedHashAlgorithm;

/**
 * Trait SecureVerificationTrait - Reusable implementation of `SecureVerificationInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\SecureVerificationInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @mixin VerificationSpecification
 * @mixin AnyDerivedHashAlgorithm
 */
trait SecureVerificationTrait
{
    /**
     * Calculates a hash value for the given data.
     *
     * @param string $data The input string.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    abstract public function hashData($data);

    /**
     * Securely compares and verifies if a digestion value is for the given input data.
     *
     * @param string $data The input string.
     * @param string $digest The digest string.
     *
     * @return bool The result of the secure comparison.
     * @throws \Exception Validation errors.
     *
     * @internal Do not forget to set the correct digestion settings for the current object before calling this method.
     */
    public function verifyHash($data, $digest)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('The data for hashing must be a string or a binary string.');
        } elseif (!is_string($digest)) {
            throw new \InvalidArgumentException('The digest must be a string or a binary string.');
        }

        $hash = $this->hashData($data);

        return hash_equals($hash, $digest);
    }
}
