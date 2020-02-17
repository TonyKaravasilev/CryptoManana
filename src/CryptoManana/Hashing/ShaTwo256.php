<?php

/**
 * The SHA-2 family SHA-256 hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class ShaTwo256 - The SHA-2 family SHA-256 hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class ShaTwo256 extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'sha256';
}
