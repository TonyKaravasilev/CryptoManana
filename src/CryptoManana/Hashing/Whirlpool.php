<?php

/**
 * The Whirlpool hashing algorithm class.
 */

namespace CryptoManana\Hashing;

use \CryptoManana\Core\Abstractions\MessageDigestion\AbstractUnkeyedHashFunction as UnkeyedHashAlgorithm;

/**
 * Class Whirlpool - The Whirlpool hashing algorithm object.
 *
 * @package CryptoManana\Hashing
 */
class Whirlpool extends UnkeyedHashAlgorithm
{
    /**
     * The internal name of the algorithm.
     */
    const ALGORITHM_NAME = 'whirlpool';
}
