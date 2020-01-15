<?php

/**
 * Trait implementation of the the ability to switch between different variations of a digestion algorithm.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\AlgorithmVariationInterface as VariationSwitchingSpecification;

/**
 * Trait AlgorithmVariationTrait - Reusable implementation of `AlgorithmVariationInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\AlgorithmVariationInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property int $algorithmVariation The internal algorithm variation property storage.
 *
 * @mixin VariationSwitchingSpecification
 *
 * @codeCoverageIgnore
 */
trait AlgorithmVariationTrait
{
    /**
     * Internal method for version range validation.
     *
     * @param int $version The version for validation checks.
     *
     * @throws \Exception Validation errors.
     */
    abstract protected function validateVersion($version);

    /**
     * Setter for the algorithm variation version property.
     *
     * @param int $version The algorithm variation version.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setAlgorithmVariation($version)
    {
        $version = filter_var(
            $version,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => static::VERSION_ONE,
                    "max_range" => static::VERSION_TWO,
                ],
            ]
        );

        if ($version === false) {
            throw new \InvalidArgumentException(
                'The algorithm variation version must be a valid integer between ' .
                static::VERSION_ONE . ' and ' . static::VERSION_TWO . '.'
            );
        }

        $this->validateVersion($version);

        $this->algorithmVariation = $version;

        return $this;
    }

    /**
     * Getter for the algorithm variation version property.
     *
     * @return int The algorithm variation version.
     */
    public function getAlgorithmVariation()
    {
        return $this->algorithmVariation;
    }
}
