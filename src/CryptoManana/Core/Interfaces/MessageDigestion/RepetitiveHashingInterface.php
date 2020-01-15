<?php

/**
 * Interface for specifying repetitive/recursive hashing for digestion algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageDigestion;

/**
 * Interface RepetitiveHashingInterface - Interface for repetitive/recursive data hashing.
 *
 * @package CryptoManana\Core\Interfaces\MessageDigestion
 */
interface RepetitiveHashingInterface
{
    /**
     * Calculates a hash value for the given data via repetitive/recursive digestion.
     *
     * @param string $data The input string.
     * @param int $iterations The number of internal iterations to perform.
     *
     * @return string The digest.
     * @throws \Exception Validation errors.
     */
    public function repetitiveHashData($data, $iterations = 2);
}
