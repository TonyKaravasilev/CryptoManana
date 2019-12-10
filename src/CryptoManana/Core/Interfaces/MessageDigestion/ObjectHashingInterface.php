<?php

/**
 * Interface for specifying object hashing for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface ObjectHashingInterface - Interface for object instance hashing.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface ObjectHashingInterface
{
    /**
     * Calculates a hash value for the serialized value of the given object.
     *
     * @param object|\stdClass $object The full path and name of the file for hashing.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function hashObject($object);
}
