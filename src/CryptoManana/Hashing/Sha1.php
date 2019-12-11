<?php

/**
 * The SHA-1 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class Sha1 - The SHA-1 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Sha1 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha1';
}
