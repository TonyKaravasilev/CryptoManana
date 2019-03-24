<?php

/**
 * Interface for specifying extra string output formats for pseudo-random generators.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface StringOutputInterface - Interface for random string generation.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface StringOutputInterface
{
    /**
     * Generate a random digit character.
     *
     * @param bool $includeZero Flag for including the zero digit (default => true).
     *
     * @return string Randomly generated digit character.
     */
    public function getDigit($includeZero = true);

    /**
     * Generate a random english letter character.
     *
     * @param bool $caseSensitive Flag for enabling case sensitive generation (default => true).
     *
     * @return string Randomly generated english letter character.
     */
    public function getLetter($caseSensitive = true);

    /**
     * Generate a random alphanumeric string.
     *
     * @param int $length The output string length (default => 1).
     * @param bool $caseSensitive Flag for enabling case sensitive generation (default => true).
     *
     * @return string Randomly generated alphanumeric string.
     */
    public function getAlphaNumeric($length = 1, $caseSensitive = true);

    /**
     * Generate a random ASCII (American Standard Code) string containing only printable characters.
     *
     * @param int $length The output string length (default => 1).
     * @param bool $includeSpace Flag for including the space character (default => true).
     *
     * @return string Randomly generated ASCII string.
     */
    public function getAscii($length = 1, $includeSpace = false);

    /**
     * Generate a random string with custom characters.
     *
     * @param int $length The output string length (default => 1).
     * @param array $characters The character map for the string generation (default => ASCII).
     *
     * @return string Randomly generated string using a custom character map.
     */
    public function getString($length = 1, array $characters = []);
}
