<?php

/**
 * Trait implementation of the derivation application or context information salting for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationContextInterface as DerivationContextSaltingSpecification;

/**
 * Trait DerivationContextTrait - Reusable implementation of `DerivationContextInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\DerivationContextInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property string $contextualString The derivation context/application information string property storage.
 *
 * @mixin DerivationContextSaltingSpecification
 */
trait DerivationContextTrait
{
    /**
     * Setter for the derivation context/application information string property.
     *
     * @param string $contextualString The derivation context/application information string.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setContextualString($contextualString)
    {
        if (!is_string($contextualString)) {
            throw new \InvalidArgumentException('The context information string must be of type string.');
        }

        $this->contextualString = $contextualString;

        return $this;
    }

    /**
     * Getter for the derivation context/application information string property.
     *
     * @return string The derivation context/application information string.
     */
    public function getContextualString()
    {
        return $this->contextualString;
    }
}
