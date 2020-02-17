<?php

/**
 * The HKDF-RIPEMD-320 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyMaterialDerivationFunction as KeyDerivationAlgorithm;

/**
 * Class HkdfRipemd320 - The HKDF-RIPEMD-320 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HkdfRipemd320 extends KeyDerivationAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd320';

    /**
     * The internal maximum length in bytes of the output digest for the algorithm.
     *
     * @internal For the current algorithm: `40 * 255 = 10200`
     */
    const ALGORITHM_MAXIMUM_OUTPUT = 10200;

    /**
     * The derivation output digest size in bytes length property storage.
     *
     * @var int The derivation output digest size in bytes length value.
     *
     * @internal The default output size in bytes for this algorithm.
     */
    protected $outputLength = 40;
}
