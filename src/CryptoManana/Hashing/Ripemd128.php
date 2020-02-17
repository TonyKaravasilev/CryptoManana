<?php

/**
 * The RIPEMD-128 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class Ripemd128 - The RIPEMD-128 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Ripemd128 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd128';
}
