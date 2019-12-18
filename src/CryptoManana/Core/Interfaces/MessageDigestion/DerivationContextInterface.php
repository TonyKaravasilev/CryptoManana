<?php

/**
 * Interface for specifying derivation application or context information salting for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface DerivationContextInterface - Interface for derivation application/context salting.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface DerivationContextInterface
{
    /**
     * Setter for the derivation context/application information string property.
     *
     * @param string $contextualString The derivation context/application information string.
     *
     * @throw \Exception Validation errors.
     */
    public function setContextualString($contextualString);

    /**
     * Getter for the derivation context/application information string property.
     *
     * @return string The derivation context/application information string.
     */
    public function getContextualString();
}
