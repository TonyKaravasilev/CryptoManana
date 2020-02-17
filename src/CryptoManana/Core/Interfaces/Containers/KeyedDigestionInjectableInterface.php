<?php

/**
 * Interface for dependency containers using the setter dependency injection type for keyed digestion services.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as HashFunction;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashFunction;

/**
 * Interface KeyedDigestionInjectableInterface - Interface specification for injection via a setter method.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface KeyedDigestionInjectableInterface
{
    /**
     * Setter for the message digestion and authentication service.
     *
     * @param HashFunction|KeyedHashFunction $hasher The message digestion authentication service or null.
     */
    public function setKeyedDigestionFunction(KeyedHashFunction $hasher);

    /**
     * Getter for the message digestion and authentication service.
     *
     * @return HashFunction|KeyedHashFunction|null The currently injected message digestion service or null.
     */
    public function getKeyedDigestionFunction();
}
