<?php

/**
 * The HMAC-MD5 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacMd5 - The HMAC-MD5 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacMd5 extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'md5';
}
