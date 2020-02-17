<?php

/**
 * The SHA-2 family SHA-384 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class ShaTwo384 - The SHA-2 family SHA-384 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class ShaTwo384 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha384';
}
