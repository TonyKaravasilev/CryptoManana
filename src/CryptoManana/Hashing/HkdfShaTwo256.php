<?php

/**
 * The SHA-2 family HKDF-SHA-256 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationAlgorithm;

/**
 * Class HkdfShaTwo256 - The SHA-2 family HKDF-SHA-256 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HkdfShaTwo256 extends KeyDerivationAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha256';

    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     *
     * @internal For the current algorithm: `32 * 255 = 8160`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = 8160;

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @internal The default output size in bytes for this algorithm.
     */
    protected $outputLength = 32;
}
