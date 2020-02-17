<?php

/**
 * The HMAC-RIPEMD-160 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacRipemd160 - The HMAC-RIPEMD-160 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacRipemd160 extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd160';
}
