<?php

/**
 * The SHA-2 family PBKDF2-SHA-512 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractIterativeSlowDerivation as SlowDerivationAlgorithm;

/**
 * Class Pbkdf2ShaTwo512 - The SHA-2 family PBKDF2-SHA-512 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Pbkdf2ShaTwo512 extends SlowDerivationAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha512';

    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     *
     * @note For the current algorithm: `PHP_INT_MAX`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = PHP_INT_MAX;

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @note The default output size in bytes for this algorithm.
     */
    protected $outputLength = 64;
}
