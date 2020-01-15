<?php

/**
 * Interface for specifying the total algorithmic cost tuning per one computation for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface AlgorithmicCostInterface - Interface for algorithmic cost tuning capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface AlgorithmicCostInterface
{
    /**
     * The minimum algorithmic cost for computations.
     */
    const MINIMUM_ALGORITHMIC_COST = 4;

    /**
     * The maximum algorithmic cost for computations.
     */
    const MAXIMUM_ALGORITHMIC_COST = 31;

    /**
     * Setter for the algorithmic/algorithmic cost property.
     *
     * @param int $cost The algorithmic cost.
     *
     * @throws \Exception Validation errors.
     */
    public function setAlgorithmicCost($cost);

    /**
     * Getter for the computational/algorithmic cost property.
     *
     * @return int The algorithmic cost.
     */
    public function getAlgorithmicCost();
}
