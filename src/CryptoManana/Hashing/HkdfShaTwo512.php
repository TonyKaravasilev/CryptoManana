<?php

/**
 * The SHA-2 family HKDF-SHA-512 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationAlgorithm;

/**
 * Class HkdfShaTwo512 - The SHA-2 family HKDF-SHA-512 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HkdfShaTwo512 extends KeyDerivationAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha512';

    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     *
     * @note For the current algorithm: `64 * 255 = 16320`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = 16320;

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @note The default output size in bytes for this algorithm.
     */
    protected $outputLength = 64;
}
