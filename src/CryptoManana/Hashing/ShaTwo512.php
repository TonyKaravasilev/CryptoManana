<?php

/**
 * The SHA-2 family SHA-512 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class ShaTwo512 - The SHA-2 family SHA-512 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class ShaTwo512 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha512';
}
