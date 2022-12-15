<?php

/**
 * Interface for static calls, used for string building and manipulations.
 */

namespace CryptoManana\Core\Interfaces\DesignPatterns;

/**
 * Interface CoreStringBuilderInterface - Static methods for core string manipulations.
 *
 * @see \CryptoManana\Core\StringBuilder For Usage.
 *
 * @package CryptoManana\Core\Interfaces\DesignPatterns
 */
interface CoreStringBuilderInterface
{
    /**
     * Check if the `mbstring` extension usage is enabled or not.
     *
     * @return bool Is the component using `mbstring`.
     */
    public static function isUsingMbString();

    /**
     * Enable or disable the `mbstring` extension usage.
     *
     * @param bool|int $bool Flag for enabling or disabling the `mbstring` usage.
     *
     * @note Use the `mbstring` extension only when you need custom encoding support.
     */
    public static function useMbString($bool = true);

    /**
     * Get the string's length.
     *
     * @param string|mixed $string The string for length measuring.
     *
     * @return int|false The string length or false on invalid parameter given.
     */
    public static function stringLength($string);

    /**
     * Make a string uppercase.
     *
     * @param string|mixed $string The string for uppercase conversion.
     *
     * @return string|false The string converted to uppercase or false on invalid parameter given.
     */
    public static function stringToUpper($string);

    /**
     * Make a string lowercase.
     *
     * @param string|mixed $string The string for lowercase conversion.
     *
     * @return string|false The string converted to lowercase or false on invalid parameter given.
     */
    public static function stringToLower($string);

    /**
     * Get a character by its encoding integer code value.
     *
     * @param int $byteValue The integer code numerical value for character conversion.
     *
     * @return string|false The wanted character string or false on invalid parameter given.
     */
    public static function getChr($byteValue);

    /**
     * Get a character's encoding integer code by its string representation.
     *
     * @param string|mixed $string The character string value for integer code conversion.
     *
     * @return int|false The wanted character code or false on invalid parameter given.
     */
    public static function getOrd($string);

    /**
     * Reverse a string.
     *
     * @param string|mixed $string The string for reversing.
     *
     * @return string|false The reversed string or false on invalid parameter given.
     */
    public static function stringReverse($string);

    /**
     * Convert a string to an array.
     *
     * @param string|mixed $string The string for conversion to array.
     * @param int $chunkLength The chunk length for string splitting.
     *
     * @return array|false The string converted to an array or false on invalid parameter given.
     */
    public static function stringSplit($string, $chunkLength = 1);

    /**
     * Replace all occurrences of the search string with the replacement string. It also supports arrays.
     *
     * @param string|array|mixed $search The string for searching or an array with multiple values.
     * @param string|array|mixed $replace The replacement string or an array with multiple values.
     * @param string|array $subject The string or array being searched and replaced on.
     * @param null|int $count This will hold the number of matched and replaced values.
     *
     * @return string|array Returns a string or an array with the replaced values.
     */
    public static function stringReplace($search, $replace, $subject, &$count = null);

    /**
     * Fully strip whitespace from every position of a string.
     *
     * @param string|mixed $string The string for full trimming.
     *
     * @return string|false The fully trimmed string or false on invalid parameter given.
     */
    public static function stringFullTrimming($string);
}
