<?php

/**
 * Trait implementation of repetitive/recursive hashing for digestion algorithms.
 */

namespace CryptoManana\Core\Traits\MessageDigestion;

use CryptoManana\Core\Interfaces\MessageDigestion\RepetitiveHashingInterface as RepetitiveHashingSpecification;
use CryptoManana\Core\Abstractions\MessageDigestion\AbstractHashAlgorithm as AnyDerivedHashAlgorithm;

/**
 * Trait RepetitiveHashingTrait - Reusable implementation of `RepetitiveHashingInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\RepetitiveHashingInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageDigestion
 *
 * @mixin RepetitiveHashingSpecification
 * @mixin AnyDerivedHashAlgorithm
 */
trait RepetitiveHashingTrait
{
    /**
     * Calculates a hash value for the given data.
     *
     * @param string $data The input string.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    abstract public function hashData($data);

    /**
     * Calculates a hash value for the given data via repetitive/recursive digestion.
     *
     * @param string $data The input string.
     * @param int $iterations The number of internal iterations to perform.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function repetitiveHashData($data, $iterations = 2)
    {
        $iterations = filter_var(
            $iterations,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 2,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($iterations === false) {
            throw new \InvalidArgumentException('The repeated hashing times must be a valid integer bigger than 1.');
        }

        $oldDigestFormat = $this->digestFormat;
        $this->digestFormat = self::DIGEST_OUTPUT_RAW;

        for ($i = 1; $i < $iterations; $i++) {
            $data = $this->hashData($data);
        }

        $this->digestFormat = $oldDigestFormat;

        return $this->hashData($data);
    }
}
