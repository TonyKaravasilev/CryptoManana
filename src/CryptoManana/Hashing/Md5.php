<?php

/**
 * The MD5 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class Md5 - The MD5 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Md5 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'md5';
}
