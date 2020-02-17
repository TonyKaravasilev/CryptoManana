<?php

/**
 * The RIPEMD-320 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class Ripemd320 - The RIPEMD-320 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Ripemd320 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd320';
}
