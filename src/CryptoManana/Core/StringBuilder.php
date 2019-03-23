<?php

/**
 * The core class for unicode string manipulations inside other framework classes.
 */

namespace CryptoManana\Core;

use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton as SingletonPattern;

/**
 * Class StringBuilder - The core component for string manipulations and encoding support.
 *
 * @package CryptoManana\Core
 */
class StringBuilder extends SingletonPattern
{
    /**
     * Internal flag to enable or disable the `mbstring` extension usage.
     *
     * @var bool Enable or disable unicode `mbstring` functions.
     */
    protected static $usingMbString = false;

    /**
     * Check if the the `mbstring` extension usage is enabled or not.
     *
     * @return bool Is the component using `mbstring`.
     */
    public static function isUsingMbString()
    {
        return self::$usingMbString;
    }

    /**
     * Enable or disable the `mbstring` extension usage.
     *
     * @internal Use the `mbstring` extension only when you need custom encoding support.
     *
     * @param bool|int $bool Flag for enabling or disabling the `mbstring` usage.
     */
    public static function useMbString($bool = true)
    {
        if ($bool == true) {
            self::$usingMbString = extension_loaded('mbstring');
        } else {
            self::$usingMbString = false;
        }
    }

    /**
     * Get the string's length.
     *
     * @param string|mixed $string The string for length measuring.
     *
     * @return int|false The string length or false on invalid parameter given.
     */
    public static function stringLength($string)
    {
        if (!is_string($string)) {
            return false;
        }

        return (self::$usingMbString) ? mb_strlen($string) : strlen($string);
    }

    /**
     * Make a string uppercase.
     *
     * @param string|mixed $string The string for uppercase conversion.
     *
     * @return string|false The string converted to uppercase or false on invalid parameter given.
     */
    public static function stringToUpper($string)
    {
        if (!is_string($string)) {
            return false;
        }

        return (self::$usingMbString) ? mb_strtoupper($string) : strtoupper($string);
    }

    /**
     * Make a string lowercase.
     *
     * @param string|mixed $string The string for lowercase conversion.
     *
     * @return string|false The string converted to lowercase or false on invalid parameter given.
     */
    public static function stringToLower($string)
    {
        if (!is_string($string)) {
            return false;
        }

        return (self::$usingMbString) ? mb_strtolower($string) : strtolower($string);
    }

    /**
     * Get a character by its encoding integer code value.
     *
     * @param int $byteValue The integer code numerical value for character conversion.
     *
     * @return string|false The wanted character string or false on invalid parameter given.
     */
    public static function getChr($byteValue)
    {
        $byteValue = filter_var(
            $byteValue,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 0,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($byteValue === false) {
            return false;
        }

        $mbStringIsAvailable = (
            self::$usingMbString &&
            function_exists('mb_chr') // PHP >= 7.2.0
        );

        return ($mbStringIsAvailable) ? mb_chr($byteValue) : chr($byteValue);
    }

    /**
     * Get a character's encoding integer code by its string representation.
     *
     * @param string|mixed $string The character string value for integer code conversion.
     *
     * @return int|false The wanted character code or false on invalid parameter given.
     */
    public static function getOrd($string)
    {
        if (!is_string($string)) {
            return false;
        }

        $mbStringIsAvailable = (
            self::$usingMbString &&
            function_exists('mb_ord') // PHP >= 7.2.0
        );

        return ($mbStringIsAvailable) ? mb_ord($string) : ord($string);
    }

    /**
     * Reverse a string.
     *
     * @param string|mixed $string The string for reversing.
     *
     * @return string|false The reversed string or false on invalid parameter given.
     */
    public static function stringReverse($string)
    {
        if (!is_string($string)) {
            return false;
        }

        if (self::$usingMbString) {
            $length = mb_strlen($string);

            $tmp = '';

            while ($length > 0) {
                $length--;

                $tmp .= mb_substr($string, $length, 1);
            }

            return $tmp;
        } else {
            return strrev($string);
        }
    }

    /**
     * Convert a string to an array.
     *
     * @param string|mixed $string The string for conversion to array.
     * @param int $chunkLength The chunk length for string splitting.
     *
     * @return array|false The string converted to an array or false on invalid parameter given.
     */
    public static function stringSplit($string, $chunkLength = 1)
    {
        if (!is_string($string) || $chunkLength < 1) {
            return false;
        }

        $length = self::stringLength($string);

        if ($chunkLength >= $length) {
            return [$string];
        }

        if (self::$usingMbString) {
            $tmp = [];

            do {
                $tmp[] = mb_substr($string, 0, $chunkLength);
                $string = mb_substr($string, $chunkLength);
            } while (!empty($string));

            return $tmp;
        } else {
            return str_split($string, $chunkLength);
        }
    }

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
    public static function stringReplace($search, $replace, $subject, &$count = null)
    {
        if (self::$usingMbString) {
            if (is_array($subject)) {
                foreach ($subject as $key => $value) {
                    $subject[$key] = self::stringReplace($search, $replace, $value, $count);
                }
            } else {
                $searches = is_array($search) ? array_values($search) : [$search];

                $replacements = is_array($replace) ? array_values($replace) : [$replace];
                $replacements = array_pad($replacements, count($searches), '');

                foreach ($searches as $key => $searched) {
                    $parts = mb_split(preg_quote($searched), $subject);
                    $count += count($parts) - 1;
                    $subject = implode($replacements[$key], $parts);
                }
            }

            return $subject;
        } else {
            // This function is Unicode ready and does not have a `mb_*` substitute
            return str_replace($search, $replace, $subject, $count);
        }
    }

    /**
     * Fully strip whitespace from every position of a string.
     *
     * @param string|mixed $string The string for full trimming.
     *
     * @return string|false The fully trimmed string or false on invalid parameter given.
     */
    public static function stringFullTrimming($string)
    {
        if (!is_string($string)) {
            return false;
        }

        return self::stringReplace([" ", "\t", "\n", "\r", "\0", "\x0B"], '', $string);
    }
}
