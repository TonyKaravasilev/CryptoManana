<?php

/**
 * Trait implementation of the total algorithmic cost via complex tuning per one computation for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use CryptoManana\Core\Interfaces\MessageDigestion\ComplexAlgorithmicCostInterface as AlgorithmicCostSpecification;

/**
 * Trait ComplexAlgorithmicCostTrait - Reusable implementation of `ComplexAlgorithmicCostInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\ComplexAlgorithmicCostInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property int $memoryCost The digestion internal computational memory cost property storage.
 * @property int $timeCost The digestion internal computational time cost property storage.
 * @property int $threadsCost The digestion internal computational thread cost property storage.
 *
 * @mixin AlgorithmicCostSpecification
 *
 * @codeCoverageIgnore
 */
trait ComplexAlgorithmicCostTrait
{
    /**
     * Setter for the memory cost property.
     *
     * @param int $cost The algorithmic memory cost.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setMemoryCost($cost)
    {
        $cost = filter_var(
            $cost,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => static::MINIMUM_MEMORY_COST,
                    "max_range" => static::MAXIMUM_MEMORY_COST,
                ],
            ]
        );

        if ($cost === false) {
            throw new \InvalidArgumentException(
                'The number of internal iterations must be a valid integer bigger than 0.'
            );
        }

        $this->memoryCost = $cost;

        return $this;
    }

    /**
     * Getter for the memory cost property.
     *
     * @return int The algorithmic memory cost.
     */
    public function getMemoryCost()
    {
        return $this->memoryCost;
    }

    /**
     * Setter for the time cost property.
     *
     * @param int $cost The algorithmic time cost.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setTimeCost($cost)
    {
        $cost = filter_var(
            $cost,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => static::MINIMUM_TIME_COST,
                    "max_range" => static::MAXIMUM_TIME_COST,
                ],
            ]
        );

        if ($cost === false) {
            throw new \InvalidArgumentException(
                'The number of internal iterations must be a valid integer bigger than 0.'
            );
        }

        $this->timeCost = $cost;

        return $this;
    }

    /**
     * Getter for the time cost property.
     *
     * @return int The algorithmic time cost.
     */
    public function getTimeCost()
    {
        return $this->timeCost;
    }

    /**
     * Setter for the threads cost property.
     *
     * @param int $cost The algorithmic threads cost.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setThreadsCost($cost)
    {
        $cost = filter_var(
            $cost,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => static::MINIMUM_THREADS_COST,
                    "max_range" => static::MAXIMUM_THREADS_COST,
                ],
            ]
        );

        if ($cost === false) {
            throw new \InvalidArgumentException(
                'The number of internal iterations must be a valid integer bigger than 0.'
            );
        }

        $this->threadsCost = $cost;

        return $this;
    }

    /**
     * Getter for the threads cost property.
     *
     * @return int The algorithmic threads cost.
     */
    public function getThreadsCost()
    {
        return $this->threadsCost;
    }
}
