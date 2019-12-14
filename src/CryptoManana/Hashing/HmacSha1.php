<?php

/**
 * The HMAC-SHA-1 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacSha1 - The HMAC-SHA-1 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacSha1 extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha1';
}
