<?php

/**
 * The RIPEMD-160 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class Ripemd160 - The RIPEMD-160 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Ripemd160 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd160';
}
