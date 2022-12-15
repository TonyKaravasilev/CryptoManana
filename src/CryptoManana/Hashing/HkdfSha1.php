<?php

/**
 * The HKDF-SHA-1 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationAlgorithm;

/**
 * Class HkdfSha1 - The HKDF-SHA-1 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HkdfSha1 extends KeyDerivationAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha1';

    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     *
     * @note For the current algorithm: `20 * 255 = 5100`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = 5100;

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @note The default output size in bytes for this algorithm.
     */
    protected $outputLength = 20;
}
