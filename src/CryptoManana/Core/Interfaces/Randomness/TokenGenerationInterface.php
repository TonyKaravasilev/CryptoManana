<?php

/**
 * Interface for security token and password string generation capabilities.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface TokenGenerationInterface - Interface for security token and password generation.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface TokenGenerationInterface
{
    /**
     * Generate a random token string in alphanumeric or hexadecimal format.
     *
     * Note: This method can generate HEX output if the `$useAlphaNumeric` parameter is set to `false`.
     *
     * @param int $length The desired output length (default => 40).
     * @param bool|int $useAlphaNumeric Flag for switching to alphanumerical (default => true).
     *
     * @return string Randomly generated alphanumeric/hexadecimal token string.
     */
    public function getTokenString($length = 40, $useAlphaNumeric = true);

    /**
     * Generate a random password string.
     *
     * Note: This method can use more special symbols on generation if the `$stronger` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 10).
     * @param bool|int $stronger Flag for using all printable ASCII characters (default => true).
     *
     * @return string Randomly generated password string.
     */
    public function getPasswordString($length = 10, $stronger = true);
}
