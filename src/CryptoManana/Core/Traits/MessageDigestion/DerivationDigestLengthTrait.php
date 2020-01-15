<?php

/**
 * Trait implementation of the derivation control over the outputting digest length for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use \CryptoManana\Core\Interfaces\MessageDigestion\DerivationDigestLengthInterface as DigestLengthSpecification;

/**
 * Trait DerivationDigestLengthTrait - Reusable implementation of `DerivationDigestLengthInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\DerivationDigestLengthInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @property int $outputLength The derivation output digest size in bytes length property storage.
 *
 * @mixin DigestLengthSpecification
 */
trait DerivationDigestLengthTrait
{
    /**
     * Setter for the derivation output digest size in bytes length property.
     *
     * @param int $byteLength The derivation output digest size in bytes length.
     *
     * @return $this The hash algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setOutputLength($byteLength)
    {
        $byteLength = filter_var(
            $byteLength,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 1,
                    "max_range" => static::ALGORITHM_MAXIMUM_OUTPUT,
                ],
            ]
        );

        if ($byteLength === false) {
            throw new \InvalidArgumentException(
                'The output length must be a valid integer and be between 1 and ' .
                static::ALGORITHM_MAXIMUM_OUTPUT . '.'
            );
        }

        $this->outputLength = $byteLength;

        return $this;
    }

    /**
     * Getter for the derivation output digest size in bytes length property.
     *
     * @return int The derivation output digest size in bytes length.
     */
    public function getOutputLength()
    {
        return $this->outputLength;
    }
}
