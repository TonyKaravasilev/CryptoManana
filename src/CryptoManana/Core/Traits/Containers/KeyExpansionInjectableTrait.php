<?php

/**
 * Trait implementation of the setter dependency injection type for key expansion digestion services.
 */

namespace CryptoManana\Core\Traits\Containers;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationFunction;
use \CryptoManana\Core\Interfaces\Containers\KeyExpansionInjectableInterface as KeyExpansionSpecification;

/**
 * Trait KeyExpansionInjectableTrait - Reusable implementation of `KeyExpansionInjectableInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\KeyExpansionInjectableInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property KeyDerivationFunction|null $keyExpansionSource The message key expansion derivation service.
 *
 * @mixin KeyExpansionSpecification
 */
trait KeyExpansionInjectableTrait
{
    /**
     * Setter for the key expansion derivation service.
     *
     * @param KeyDerivationFunction $hasher The key expansion derivation service or null.
     *
     * @return $this The container object.
     */
    public function setKeyExpansionFunction(KeyDerivationFunction $hasher)
    {
        $this->keyExpansionSource = $hasher;

        return $this;
    }

    /**
     * Getter for the key expansion derivation service.
     *
     * @return KeyDerivationFunction|null The currently injected key expansion derivation service or null.
     */
    public function getKeyExpansionFunction()
    {
        return $this->keyExpansionSource;
    }
}
