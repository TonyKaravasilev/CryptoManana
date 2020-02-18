<?php

/**
 * Trait implementation of the derivation control over the internal number of iterations for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use CryptoManana\Core\Interfaces\MessageDigestion\DerivationIterationControlInterface as IterationControlSpecification;

/**
 * Trait DerivationIterationControlTrait - Reusable implementation of `DerivationIterationControlInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\DerivationIterationControlInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property int $numberOfIterations The derivation internal iteration count property storage.
 *
 * @mixin IterationControlSpecification
 */
trait DerivationIterationControlTrait
{
    /**
     * Setter for the derivation internal iteration count property.
     *
     * @param int $numberOfIterations The number of internal iterations to perform.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setDerivationIterations($numberOfIterations)
    {
        $numberOfIterations = filter_var(
            $numberOfIterations,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 1,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($numberOfIterations === false) {
            throw new \InvalidArgumentException(
                'The number of internal iterations must be a valid integer bigger than 0.'
            );
        }

        $this->numberOfIterations = $numberOfIterations;

        return $this;
    }

    /**
     * Getter for the derivation internal iteration count property.
     *
     * @return int The number of internal iterations to perform.
     */
    public function getDerivationIterations()
    {
        return $this->numberOfIterations;
    }
}
