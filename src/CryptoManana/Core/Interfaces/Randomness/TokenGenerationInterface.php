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
     * The paranoid-enough password character length requirement.
     */
    const PARANOID_PASSWORD_LENGTH = 40;

    /**
     * The strong password character length requirement.
     */
    const STRONG_PASSWORD_LENGTH = 20;

    /**
     * The moderate password character length requirement.
     */
    const MODERATE_PASSWORD_LENGTH = 12;

    /**
     * The weak password character length requirement.
     */
    const WEAK_PASSWORD_LENGTH = 8;

    /**
     * The paranoid-enough token character length requirement.
     */
    const PARANOID_TOKEN_LENGTH = 128;

    /**
     * The strong token character length requirement.
     */
    const STRONG_TOKEN_LENGTH = 64;

    /**
     * The moderate token character length requirement.
     */
    const MODERATE_TOKEN_LENGTH = 32;

    /**
     * The weak token character length requirement.
     */
    const WEAK_TOKEN_LENGTH = 16;

    /**
     * Generate a random token string in alphanumeric or hexadecimal format.
     *
     * Note: This method can generate HEX output if the `$useAlphaNumeric` parameter is set to `false`.
     *
     * @param int $length The desired output length (default => 32).
     * @param bool|int $useAlphaNumeric Flag for switching to alphanumerical (default => true).
     *
     * @return string Randomly generated alphanumeric/hexadecimal token string.
     */
    public function getTokenString($length = self::MODERATE_TOKEN_LENGTH, $useAlphaNumeric = true);

    /**
     * Generate a random password string.
     *
     * Note: This method can use more special symbols on generation if the `$stronger` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 12).
     * @param bool|int $stronger Flag for using all printable ASCII characters (default => true).
     *
     * @return string Randomly generated password string.
     */
    public function getPasswordString($length = self::MODERATE_PASSWORD_LENGTH, $stronger = true);
}
