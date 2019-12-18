<?php

/**
 * Interface for specifying derivation salting capabilities for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface DerivationSaltingInterface - Interface for derivation salting capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface DerivationSaltingInterface
{
    /**
     * Setter for the derivation salt string property.
     *
     * @param string $derivationSalt The derivation salt string.
     *
     * @throw \Exception Validation errors.
     */
    public function setDerivationSalt($derivationSalt);

    /**
     * Getter for the derivation salt string property.
     *
     * @return string The derivation salt string.
     */
    public function getDerivationSalt();
}
