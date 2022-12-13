<?php

/**
 * The SHA-2 family HKDF-SHA-384 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationAlgorithm;

/**
 * Class HkdfShaTwo384 - The SHA-2 family HKDF-SHA-384 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HkdfShaTwo384 extends KeyDerivationAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha384';

    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     *
     * @note For the current algorithm: `48 * 255 = 12240`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = 12240;

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @note The default output size in bytes for this algorithm.
     */
    protected $outputLength = 48;
}
