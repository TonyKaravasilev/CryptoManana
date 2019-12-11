<?php

/**
 * The SHA-2 family SHA-224 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class ShaTwo224 - The SHA-2 family SHA-224 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class ShaTwo224 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha224';
}
