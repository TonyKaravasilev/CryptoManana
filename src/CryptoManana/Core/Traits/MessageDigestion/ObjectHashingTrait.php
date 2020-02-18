<?php

/**
 * Trait implementation of object hashing for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as AnyDerivedHashAlgorithm;
use CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface as ObjectHashingSpecification;

/**
 * Trait ObjectHashingTrait - Reusable implementation of `ObjectHashingInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\ObjectHashingInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @mixin ObjectHashingSpecification
 * @mixin AnyDerivedHashAlgorithm
 */
trait ObjectHashingTrait
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
     * Calculates a hash value for the serialized value of the given object.
     *
     * @param object|\stdClass $object The object for hashing.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function hashObject($object)
    {
        if (is_object($object)) {
            $object = serialize($object);
        } else {
            throw new \InvalidArgumentException('The data for hashing must be an object instance.');
        }

        return $this->hashData($object);
    }
}
