<?php

/**
 * Trait implementation of the total algorithmic cost tuning per one computation for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\AlgorithmicCostInterface as AlgorithmicCostSpecification;

/**
 * Trait AlgorithmicCostTrait - Reusable implementation of `AlgorithmicCostInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\AlgorithmicCostInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property int $computationalCost The digestion internal computational cost property storage.
 *
 * @mixin AlgorithmicCostSpecification
 */
trait AlgorithmicCostTrait
{
    /**
     * Setter for the computational cost property.
     *
     * @param int $cost The algorithmic cost.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setAlgorithmicCost($cost)
    {
        $cost = filter_var(
            $cost,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => static::MINIMUM_ALGORITHMIC_COST,
                    "max_range" => static::MAXIMUM_ALGORITHMIC_COST,
                ],
            ]
        );

        if ($cost === false) {
            throw new \InvalidArgumentException(
                'The number of internal iterations must be a valid integer bigger than 0.'
            );
        }

        $this->computationalCost = $cost;

        return $this;
    }

    /**
     * Getter for the computational cost property.
     *
     * @return int The algorithmic cost.
     */
    public function getAlgorithmicCost()
    {
        return $this->computationalCost;
    }
}
