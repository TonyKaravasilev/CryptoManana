<?php

/**
 * The HKDF-SHA-3 pure PHP implementation that is compatible with PHP versions before 7.1 and older `hash` extensions.
 */

namespace CryptoManana\Compatibility;

use CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton as SingletonPattern;
use CryptoManana\Compatibility\NativeHmacSha3 as HmacSha3;

/**
 * Class NativeHkdfSha3 - Pure PHP implementation of the HKDF-SHA-3 algorithm.
 *
 * @see HmacSha3 For internal plain HMAC-SHA-3 digest generation.
 *
 * @package CryptoManana\Compatibility
 */
class NativeHkdfSha3 extends SingletonPattern
{
    /**
     * Internal flag to enable or disable the `mbstring` extension usage.
     *
     * Note: `null` => auto-check on next call, `true` => available, `false` => not available.
     *
     * @var null|bool Is the `mbstring` extension supported.
     */
    protected static $mbString = null;

    /**
     * Internal storage of the SHA-3 algorithm sizes.
     *
     * @var array The algorithm sizes information.
     */
    protected static $sizes = [
        'sha3-224' => 28,
        'sha3-256' => 32,
        'sha3-384' => 48,
        'sha3-512' => 64,
    ];

    /**
     * Internal static method for single point consumption of the HKDF-SHA-3 implementation.
     *
     * @param string $algorithm The SHA-3 algorithm name.
     * @param string|mixed $ikm The input keying material (cannot be empty).
     * @param int $length The desired output string length in bytes.
     * @param string|mixed $info The application or context-specific string.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    protected static function customHkdf($algorithm, $ikm, $length = 0, $info = '', $salt = '', $rawOutput = false)
    {
        if (self::$mbString === null) {
            self::$mbString = extension_loaded('mbstring');
        }

        switch ($algorithm) {
            case 'sha3-224':
                $method = 'digest224';
                break;
            case 'sha3-256':
                $method = 'digest256';
                break;
            case 'sha3-384':
                $method = 'digest384';
                break;
            case 'sha3-512':
                $method = 'digest512';
                break;
            default:
                $method = '';
                break;
        }

        if (empty($method)) {
            throw new \RuntimeException('The internal algorithm is not found.');
        }

        if (!is_string($ikm)) {
            throw new \InvalidArgumentException('The IKM parameter must be of type string.');
        }

        if (!is_string($info)) {
            throw new \InvalidArgumentException('The info parameter must be of type string.');
        }

        if (!is_string($salt)) {
            throw new \InvalidArgumentException('The salt parameter must be of type string.');
        }

        if (empty($ikm)) {
            throw new \InvalidArgumentException('The Input keying material cannot be empty.');
        }

        $length = filter_var($length, FILTER_VALIDATE_INT);

        if ($length === false) {
            throw new \InvalidArgumentException('The output length must be of type integer.');
        } elseif ($length < 0) {
            throw new \InvalidArgumentException('The output length must be greater than or equal to 0.');
        } elseif ($length > (255 * self::$sizes[$algorithm])) {
            throw new \InvalidArgumentException(
                'The output Length must be less than or equal to ' . (255 * self::$sizes[$algorithm]) . '.'
            );
        } elseif ($length === 0) {
            $length = self::$sizes[$algorithm]; // Use default
        }

        if (empty($salt)) {
            $salt = str_repeat("\x0", self::$sizes[$algorithm]); // Create empty bytes
        }

        $prk = HmacSha3::{$method}($ikm, $salt, true);
        $okm = '';

        for ($keyBlock = '', $blockIndex = 1; !isset($okm[$length - 1]); $blockIndex++) {
            // Note: mb_chr() is available only in PHP >= 7.2, so using chr() in ASCII 8-bit codes
            $tmp = chr($blockIndex);

            $keyBlock = HmacSha3::{$method}($keyBlock . $info . $tmp, $prk, true);

            $okm .= $keyBlock;
        }

        unset($tmp);

        $digest = self::$mbString ? mb_substr($okm, 0, $length, '8bit') : substr($okm, 0, $length);

        return ($rawOutput == true) ? $digest : bin2hex($digest);
    }

    /**
     * The HKDF-SHA-3-224 key derivation function.
     *
     * @param string $ikm The input keying material (cannot be empty).
     * @param int $length The desired output string length in bytes.
     * @param string $information The application or context-specific string.
     * @param string $salt The salt string to use during derivation.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    public static function digest224($ikm, $length = 0, $information = '', $salt = '', $rawOutput = false)
    {
        return self::customHkdf('sha3-224', $ikm, $length, $information, $salt, $rawOutput);
    }

    /**
     * The HKDF-SHA-3-256 key derivation function.
     *
     * @param string|mixed $ikm The input keying material (cannot be empty).
     * @param int $length The desired output string length in bytes.
     * @param string|mixed $information The application or context-specific string.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    public static function digest256($ikm, $length = 0, $information = '', $salt = '', $rawOutput = false)
    {
        return self::customHkdf('sha3-256', $ikm, $length, $information, $salt, $rawOutput);
    }

    /**
     * The HKDF-SHA-3-384 key derivation function.
     *
     * @param string|mixed $ikm The input keying material (cannot be empty).
     * @param int $length The desired output string length in bytes.
     * @param string|mixed $information The application or context-specific string.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    public static function digest384($ikm, $length = 0, $information = '', $salt = '', $rawOutput = false)
    {
        return self::customHkdf('sha3-384', $ikm, $length, $information, $salt, $rawOutput);
    }

    /**
     * The HKDF-SHA-3-512 key derivation function.
     *
     * @param string|mixed $ikm The input keying material (cannot be empty).
     * @param int $length The desired output string length in bytes.
     * @param string|mixed $information The application or context-specific string.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    public static function digest512($ikm, $length = 0, $information = '', $salt = '', $rawOutput = false)
    {
        return self::customHkdf('sha3-512', $ikm, $length, $information, $salt, $rawOutput);
    }
}
