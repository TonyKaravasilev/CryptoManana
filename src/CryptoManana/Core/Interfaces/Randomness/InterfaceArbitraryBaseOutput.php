<?php

/**
 * Interface for specifying extra arbitrary base output formats for pseudo-random generators.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface InterfaceArbitraryBaseOutput - Interface for arbitrary base number generation.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface InterfaceArbitraryBaseOutput
{
    /**
     * Generate a random boolean.
     *
     * @return bool Randomly generated boolean value.
     */
    public function getBool();

    /**
     * Generate a random ternary format (-1, 0, 1).
     *
     * Note: Passing `false` to the `$asInteger` parameter will convert values to `null`, `false` and `true`.
     *
     * @param bool|int $asInteger Flag for returning as integer (default => true).
     *
     * @return bool|int Randomly generated ternary value.
     */
    public function getTernary($asInteger = true);

    /**
     * Generate a random HEX string.
     *
     * @param int $length The output string length (default => 1).
     * @param bool $upperCase Flag for using uppercase output (default => false).
     *
     * @return string Randomly generated HEX string.
     */
    public function getHex($length = 1, $upperCase = false);

    /**
     * Generate a random Base64 string.
     *
     * @param int $length The internal byte string length (default => 1).
     * @param bool $urlFriendly Flag for using URL friendly output (default => false).
     *
     * @return string Randomly generated Base64 RFC 4648 standard string.
     */
    public function getBase64($length = 1, $urlFriendly = false);
}
