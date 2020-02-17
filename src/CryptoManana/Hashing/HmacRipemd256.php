<?php

/**
 * The HMAC-RIPEMD-256 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacRipemd256 - The HMAC-RIPEMD-256 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacRipemd256 extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'ripemd256';
}
