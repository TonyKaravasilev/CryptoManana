<?php

/**
 * Trait implementation of the setter dependency injection type for keyed digestion services.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashFunction;
use \CryptoManana\Core\Interfaces\Containers\KeyedDigestionInjectableInterface as KeyedDigestionSpecification;

/**
 * Trait KeyedDigestionInjectableTrait - Reusable implementation of `KeyedDigestionInjectableInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\KeyedDigestionInjectableInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property KeyedHashFunction|null $keyedDigestionSource The message keyed digestion service.
 *
 * @mixin KeyedDigestionSpecification
 */
trait KeyedDigestionInjectableTrait
{
    /**
     * Setter for the keyed message digestion service.
     *
     * @param KeyedHashFunction $hasher The keyed message digestion service or null.
     *
     * @return $this The container object.
     */
    public function setKeyedDigestionFunction(KeyedHashFunction $hasher)
    {
        $this->keyedDigestionSource = $hasher;

        return $this;
    }

    /**
     * Getter for the keyed message digestion service.
     *
     * @return KeyedHashFunction|null The currently injected message digestion service or null.
     */
    public function getKeyedDigestionFunction()
    {
        return $this->keyedDigestionSource;
    }
}
