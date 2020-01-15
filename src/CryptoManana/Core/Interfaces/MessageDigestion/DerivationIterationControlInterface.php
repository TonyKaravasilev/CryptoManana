<?php

/**
 * Interface for specifying the derivation control over the internal number of iterations for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface DerivationIterationControlInterface - Interface for internal iteration control capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface DerivationIterationControlInterface
{
    /**
     * Setter for the derivation internal iteration count property.
     *
     * @param int $numberOfIterations The number of internal iterations to perform.
     *
     * @throws \Exception Validation errors.
     */
    public function setDerivationIterations($numberOfIterations);

    /**
     * Getter for the derivation internal iteration count property.
     *
     * @return int The number of internal iterations to perform.
     */
    public function getDerivationIterations();
}
