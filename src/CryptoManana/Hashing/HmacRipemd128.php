<?php

/**
 * The HMAC-RIPEMD-128 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacRipemd128 - The HMAC-RIPEMD-128 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacRipemd128 extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd128';
}
