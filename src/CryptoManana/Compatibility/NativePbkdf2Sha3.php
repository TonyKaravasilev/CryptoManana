<?php

/**
 * The PBKD2-SHA-3 pure PHP implementation that is compatible with PHP versions before 7.1 and older `hash` extensions.
 */

namespace CryptoManana\Compatibility;

use CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton as SingletonPattern;
use CryptoManana\Compatibility\NativeHmacSha3 as HmacSha3;

/**
 * Class NativePbkdf2Sha3 - Pure PHP implementation of the PBKDF2-SHA-3 algorithm.
 *
 * @see HmacSha3 For internal plain HMAC-SHA-3 digest generation.
 *
 * @package CryptoManana\Compatibility
 */
class NativePbkdf2Sha3 extends SingletonPattern
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
     * Internal static method for single point consumption of the PBKD2-SHA-3 implementation.
     *
     * @param string $algorithm The SHA-3 algorithm name.
     * @param string|mixed $password The password to use for the derivation.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param int $iterations The number of internal iterations to perform for the derivation.
     * @param int $keyLength The length of the output derivation key string.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    protected static function customPbkdf2($algorithm, $password, $salt, $iterations, $keyLength, $rawOutput = false)
    {
        if (self::$mbString === null) {
            self::$mbString = extension_loaded('mbstring');
        }

        switch ($algorithm) {
            case 'sha3-224':
                $hashLengthInBytes = 28;
                $method = 'digest224';
                break;
            case 'sha3-256':
                $hashLengthInBytes = 32;
                $method = 'digest256';
                break;
            case 'sha3-384':
                $hashLengthInBytes = 48;
                $method = 'digest384';
                break;
            case 'sha3-512':
                $hashLengthInBytes = 64;
                $method = 'digest512';
                break;
            default:
                $hashLengthInBytes = 0;
                $method = '';
                break;
        }

        if (empty($method) || empty($hashLengthInBytes)) {
            throw new \RuntimeException('The internal algorithm is not found.');
        }

        if (!is_string($password)) {
            throw new \InvalidArgumentException('The password parameter must be of type string.');
        }

        if (!is_string($salt)) {
            throw new \InvalidArgumentException('The salt parameter must be of type string.');
        }

        if ($iterations <= 0) {
            throw new \InvalidArgumentException('The iteration count must be greater than zero.');
        }

        if ($keyLength < 0) {
            throw new \InvalidArgumentException('The key length must be greater or equal zero.');
        }

        /**
         * {@internal No salt length check (`strlen($saltLength) > PHP_INT_MAX - 4`) since no warning is emitted. }}
         */

        if ($keyLength === 0) {
            $keyLength = $hashLengthInBytes;
        }

        $blockCount = ceil($keyLength / $hashLengthInBytes);

        $output = "";

        for ($i = 1; $i <= $blockCount; $i++) {
            // The $i is encoded as 4 bytes, big endian.
            $last = $salt . pack('N', $i);

            // First iteration
            $last = $xorSum = HmacSha3::{$method}($last, $password, true);

            // Perform the other $count - 1 iterations
            for ($j = 1; $j < $iterations; $j++) {
                $xorSum ^= ($last = HmacSha3::{$method}($last, $password, true));
            }

            $output .= $xorSum;
        }

        if ($rawOutput == false) {
            $output = bin2hex($output);
        }

        return self::$mbString ? mb_substr($output, 0, $keyLength, '8bit') : substr($output, 0, $keyLength);
    }

    /**
     * The PBKD2-SHA-3-224 key derivation function.
     *
     * @param string|mixed $password The password to use for the derivation.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param int $iterations The number of internal iterations to perform for the derivation.
     * @param int $keyLength The length of the output derivation key string.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    public static function digest224($password, $salt, $iterations, $keyLength, $rawOutput = false)
    {
        return self::customPbkdf2('sha3-224', $password, $salt, $iterations, $keyLength, $rawOutput);
    }

    /**
     * The PBKD2-SHA-3-256 key derivation function.
     *
     * @param string|mixed $password The password to use for the derivation.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param int $iterations The number of internal iterations to perform for the derivation.
     * @param int $keyLength The length of the output derivation key string.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    public static function digest256($password, $salt, $iterations, $keyLength, $rawOutput = false)
    {
        return self::customPbkdf2('sha3-256', $password, $salt, $iterations, $keyLength, $rawOutput);
    }

    /**
     * The PBKD2-SHA-3-384 key derivation function.
     *
     * @param string|mixed $password The password to use for the derivation.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param int $iterations The number of internal iterations to perform for the derivation.
     * @param int $keyLength The length of the output derivation key string.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    public static function digest384($password, $salt, $iterations, $keyLength, $rawOutput = false)
    {
        return self::customPbkdf2('sha3-384', $password, $salt, $iterations, $keyLength, $rawOutput);
    }

    /**
     * The PBKD2-SHA-3-512 key derivation function.
     *
     * @param string|mixed $password The password to use for the derivation.
     * @param string|mixed $salt The salt string to use during derivation.
     * @param int $iterations The number of internal iterations to perform for the derivation.
     * @param int $keyLength The length of the output derivation key string.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output derived key (output keying material).
     * @throws \Exception Validation errors.
     */
    public static function digest512($password, $salt, $iterations, $keyLength, $rawOutput = false)
    {
        return self::customPbkdf2('sha3-512', $password, $salt, $iterations, $keyLength, $rawOutput);
    }
}
