<?php

/**
 * Interface for specifying keyed hashing capabilities for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface DigestionKeyInterface - Interface for keyed hashing capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface DigestionKeyInterface
{
    /**
     * Setter for the key string property.
     *
     * @param string $key The digestion key string.
     *
     * @throw \Exception Validation errors.
     */
    public function setKey($key);

    /**
     * Getter for the key string property.
     *
     * @return string The digestion key string.
     */
    public function getKey();
}
