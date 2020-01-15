<?php

/**
 * Interface for specifying the total algorithmic cost via complex tuning per one computation for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface ComplexAlgorithmicCostInterface - Interface for complex algorithmic cost tuning capabilities.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface ComplexAlgorithmicCostInterface
{
    /**
     * The minimum time cost for computations.
     */
    const MINIMUM_MEMORY_COST = 1;

    /**
     * The maximum time cost for computations.
     */
    const MAXIMUM_MEMORY_COST = PHP_INT_MAX;

    /**
     * The minimum time cost for computations.
     */
    const MINIMUM_TIME_COST = 1;

    /**
     * The maximum time cost for computations.
     */
    const MAXIMUM_TIME_COST = PHP_INT_MAX;

    /**
     * The minimum threads cost for computations.
     */
    const MINIMUM_THREADS_COST = 1;

    /**
     * The maximum threads cost for computations.
     */
    const MAXIMUM_THREADS_COST = PHP_INT_MAX;

    /**
     * Setter for the memory cost property.
     *
     * @param int $cost The algorithmic memory cost.
     *
     * @throws \Exception Validation errors.
     */
    public function setMemoryCost($cost);

    /**
     * Getter for the memory cost property.
     *
     * @return int The algorithmic memory cost.
     */
    public function getMemoryCost();

    /**
     * Setter for the time cost property.
     *
     * @param int $cost The algorithmic time cost.
     *
     * @throws \Exception Validation errors.
     */
    public function setTimeCost($cost);

    /**
     * Getter for the time cost property.
     *
     * @return int The algorithmic time cost.
     */
    public function getTimeCost();

    /**
     * Setter for the threads cost property.
     *
     * @param int $cost The algorithmic threads cost.
     *
     * @throws \Exception Validation errors.
     */
    public function setThreadsCost($cost);

    /**
     * Getter for the threads cost property.
     *
     * @return int The algorithmic threads cost.
     */
    public function getThreadsCost();
}
