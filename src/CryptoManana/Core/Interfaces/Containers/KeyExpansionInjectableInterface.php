<?php

/**
 * Interface for dependency containers using the setter dependency injection type for key expansion digestion services.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationFunction;

/**
 * Interface KeyExpansionInjectableInterface - Interface specification for injection via a setter method.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface KeyExpansionInjectableInterface
{
    /**
     * Setter for the key expansion derivation service.
     *
     * @param KeyDerivationFunction $hasher The key expansion derivation service or null.
     */
    public function setKeyExpansionFunction(KeyDerivationFunction $hasher);

    /**
     * Getter for the key expansion derivation service.
     *
     * @return KeyDerivationFunction|null The currently injected key expansion derivation service or null.
     */
    public function getKeyExpansionFunction();
}
