<?php

/**
 * Trait implementation of the keyed hashing capabilities for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use CryptoManana\Core\Interfaces\MessageDigestion\DigestionKeyInterface as DigestionKeySpecification;

/**
 * Trait DigestionKeyTrait - Reusable implementation of `DigestionKeyInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\DigestionKeyInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property string $key The key string property storage.
 *
 * @mixin DigestionKeySpecification
 */
trait DigestionKeyTrait
{
    /**
     * Setter for the key string property.
     *
     * @param string $key The digestion key string.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setKey($key)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException('The key must be of type string.');
        }

        $this->key = $key;

        return $this;
    }

    /**
     * Getter for the key string property.
     *
     * @return string The digestion key string.
     */
    public function getKey()
    {
        return $this->key;
    }
}
