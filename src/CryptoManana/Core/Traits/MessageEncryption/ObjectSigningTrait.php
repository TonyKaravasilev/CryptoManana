<?php

/**
 * Trait implementation of the object signing/verifying for asymmetric signature algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use \CryptoManana\Core\Interfaces\MessageEncryption\ObjectSigningInterface as ObjectSigningSpecification;
use \CryptoManana\Core\Interfaces\MessageEncryption\DataSigningInterface as DataSigningSpecification;

/**
 * Trait ObjectSigningTrait - Reusable implementation of `ObjectSigningInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\ObjectSigningInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @mixin ObjectSigningSpecification
 * @mixin DataSigningSpecification
 */
trait ObjectSigningTrait
{
    /**
     * Generates a signature of the given object.
     *
     * @param object|\stdClass $object The object for signing.
     *
     * @return string The signature data.
     * @throws \Exception Validation errors.
     */
    public function signObject($object)
    {
        if (is_object($object)) {
            $object = serialize($object);
        } else {
            throw new \InvalidArgumentException('The data for signing must be an object instance.');
        }

        return $this->signData($object);
    }

    /**
     * Verifies that the signature is correct for the given object.
     *
     * @param string $signatureData The signature input string.
     * @param object|\stdClass $object The object used for signing.
     *
     * @return bool The verification result.
     * @throws \Exception Validation errors.
     */
    public function verifyObjectSignature($signatureData, $object)
    {
        if (!is_string($signatureData)) {
            throw new \InvalidArgumentException('The signature data must be a string or a binary string.');
        } elseif (!is_object($object)) {
            throw new \InvalidArgumentException('The data for signing must be an object instance.');
        }

        $object = serialize($object);

        return $this->verifyDataSignature($signatureData, $object);
    }
}
