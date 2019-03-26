<?php

/**
 * Trait implementation of arbitrary base formats generation for generator services.
 */

namespace CryptoManana\Core\Traits\Randomness;

use \CryptoManana\Core\StringBuilder as StringBuilder;

/**
 * Trait ArbitraryBaseOutputTrait - Reusable implementation of `ArbitraryBaseOutputInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Randomness\ArbitraryBaseOutputInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Randomness
 */
trait ArbitraryBaseOutputTrait
{
    /**
     * Generate a random boolean.
     *
     * @return bool Randomly generated boolean value.
     * @throws \Exception Validation errors.
     */
    public function getBool()
    {
        return $this->getInt() % 2 === 0;
    }

    /**
     * Generate a random ternary format (-1, 0, 1).
     *
     * Note: Passing `false` to the `$asInteger` parameter will convert values to `null`, `false` and `true`.
     *
     * @param bool|int $asInteger Flag for returning as integer (default => true).
     *
     * @return bool|int Randomly generated ternary value.
     * @throws \Exception Validation errors.
     */
    public function getTernary($asInteger = true)
    {
        $ternary = $this->getInt(-1, 1);

        if ($asInteger) {
            return $ternary;
        } else {
            switch ($ternary) {
                case 1:
                    return true;
                case -1:
                    return null;
                default: // case 0:
                    return false;
            }
        }
    }

    /**
     * Generate a random HEX string.
     *
     * @param int $length The output string length (default => 1).
     * @param bool $upperCase Flag for using uppercase output (default => false).
     *
     * @return string Randomly generated HEX string.
     * @throws \Exception Validation errors.
     */
    public function getHex($length = 1, $upperCase = false)
    {
        $hexString = bin2hex($this->getBytes($length));

        return ($upperCase) ? StringBuilder::stringToUpper($hexString) : $hexString;
    }

    /**
     * Generate a random Base64 string.
     *
     * @param int $length The internal byte string length (default => 1).
     * @param bool $urlFriendly Flag for using URL friendly output (default => false).
     *
     * @return string Randomly generated Base64 RFC 4648 standard string.
     * @throws \Exception Validation errors.
     */
    public function getBase64($length = 1, $urlFriendly = false)
    {
        $base64 = base64_encode($this->getBytes($length));

        if ($urlFriendly) {
            return StringBuilder::stringReplace(['+', '/', '='], ['-', '_', ''], $base64);
        } else {
            return $base64;
        }
    }
}
