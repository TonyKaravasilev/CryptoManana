<?php

/**
 * The HMAC-Whirlpool hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractKeyedHashFunction as KeyedHashAlgorithm;

/**
 * Class HmacWhirlpool - The HMAC-Whirlpool hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class HmacWhirlpool extends KeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'whirlpool';
}
