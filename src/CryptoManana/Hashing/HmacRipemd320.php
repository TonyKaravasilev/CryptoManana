<?php

/**
 * The HMAC-RIPEMD-320 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacRipemd320 - The HMAC-RIPEMD-320 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacRipemd320 extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd320';
}
