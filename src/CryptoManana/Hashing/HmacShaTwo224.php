<?php

/**
 * The SHA-2 family HMAC-SHA-224 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacShaTwo224 - The SHA-2 family HMAC-SHA-224 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacShaTwo224 extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha224';
}
