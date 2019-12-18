<?php

/**
 * Interface for specifying the derivation control over the outputting digest length for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface DerivationDigestLengthInterface - Interface for derivation digest length control capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface DerivationDigestLengthInterface
{
    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     */
    const ALGORITHM_MAXIMUM_OUTPUT = 0;

    /**
     * Setter for the derivation output digest size in bytes length property.
     *
     * @param int $byteLength The derivation output digest size in bytes length.
     *
     * @throw \Exception Validation errors.
     */
    public function setOutputLength($byteLength);

    /**
     * Getter for the derivation output digest size in bytes length property.
     *
     * @return int The derivation output digest size in bytes length.
     */
    public function getOutputLength();
}
