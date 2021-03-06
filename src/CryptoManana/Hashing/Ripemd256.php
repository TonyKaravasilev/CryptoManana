<?php

/**
 * The RIPEMD-256 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class Ripemd256 - The RIPEMD-256 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Ripemd256 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd256';
}
