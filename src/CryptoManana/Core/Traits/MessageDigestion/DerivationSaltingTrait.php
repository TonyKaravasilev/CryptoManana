<?php

/**
 * Trait implementation of the derivation salting capabilities for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationSaltingInterface as DerivationSaltingSpecification;

/**
 * Trait SaltingCapabilitiesTrait - Reusable implementation of `DerivationSaltingInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\DerivationSaltingInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property string $derivationSalt The derivation salt string property storage.
 *
 * @mixin DerivationSaltingSpecification
 */
trait DerivationSaltingTrait
{
    /**
     * Setter for the derivation salt string property.
     *
     * @param string $derivationSalt The derivation salt string.
     *
     * @return $this The hash algorithm object.
     * @throw \Exception Validation errors.
     */
    public function setDerivationSalt($derivationSalt)
    {
        if (!is_string($derivationSalt)) {
            throw new \InvalidArgumentException('The derivation salt must be of type string.');
        }

        $this->derivationSalt = $derivationSalt;

        return $this;
    }

    /**
     * Getter for the derivation salt string property.
     *
     * @return string The derivation salt string.
     */
    public function getDerivationSalt()
    {
        return $this->derivationSalt;
    }
}
