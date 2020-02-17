<?php

/**
 * The HMAC-SHA-3 pure PHP implementation that is compatible with PHP versions before 7.1 and older `hash` extensions.
 */

namespace CryptoManana\Compatibility;

use CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton as SingletonPattern;
use CryptoManana\Compatibility\NativeSha3 as Sha3;

/**
 * Class NativeHmacSha3 - Pure PHP implementation of the HMAC-SHA-3 algorithm.
 *
 * @see Sha3 For internal plain SHA-3 digest generation.
 *
 * @package CryptoManana\Compatibility
 */
class NativeHmacSha3 extends SingletonPattern
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
     * Internal static method for single point consumption of the HMAC-SHA-3 implementation.
     *
     * @param string $algorithm The SHA-3 algorithm name.
     * @param string|mixed $data The input data for hashing.
     * @param string|mixed $key The hashing key or password.
     * @param bool|int|null $rawOutput Flag for using raw byte output instead of HEX.
     *
     * @return string The output HMAC digest.
     * @throws \Exception Validation errors.
     */
    protected static function customHmac($algorithm, $data, $key, $rawOutput = false)
    {
        if (self::$mbString === null) {
            self::$mbString = extension_loaded('mbstring');
        }

        switch ($algorithm) {
            case 'sha3-224':
                $outputLength = 56;
                $blockSize = 144;
                $method = 'digest224';
                break;
            case 'sha3-256':
                $outputLength = 64;
                $blockSize = 136;
                $method = 'digest256';
                break;
            case 'sha3-384':
                $outputLength = 96;
                $blockSize = 104;
                $method = 'digest384';
                break;
            case 'sha3-512':
                $outputLength = 128;
                $blockSize = 72;
                $method = 'digest512';
                break;
            default:
                $outputLength = 0;
                $blockSize = 0;
                $method = '';
                break;
        }

        if (empty($method) || empty($blockSize) || empty($outputLength)) {
            throw new \RuntimeException('The internal algorithm is not found.');
        }

        if (!is_string($data)) {
            throw new \InvalidArgumentException('The input data parameter must be of type string.');
        }

        if (!is_string($key)) {
            throw new \InvalidArgumentException('The input key parameter must be of type string.');
        }

        $length = self::$mbString ? mb_strlen($key, '8bit') : strlen($key);

        if ($length > $blockSize) {
            $key = Sha3::{$method}($key, true);
        } elseif ($length < $blockSize) {
            // Note: mb_chr() is available only in PHP >= 7.2, so using chr() in ASCII 8-bit codes
            $key = str_pad($key, $blockSize, "\x0", STR_PAD_RIGHT);
        }

        // Note: mb_ord() is available only in PHP >= 7.2, so using ord() in ASCII 8-bit codes
        $oPad = str_repeat(chr(0x5C), $blockSize);
        $iPad = str_repeat(chr(0x36), $blockSize);

        // Recalculate length
        $length = self::$mbString ? mb_strlen($key, '8bit') : strlen($key);

        // Safe XOR of two arbitrary strings
        for ($i = 0; $i < $length; $i++) {
            $oPad[$i] = $oPad[$i] ^ $key[$i];
            $iPad[$i] = $iPad[$i] ^ $key[$i];
        }

        return Sha3::{$method}($oPad . Sha3::{$method}($iPad . $data, true), $rawOutput);
    }

    /**
     * The HMAC-SHA-3-224 hashing function.
     *
     * @param string|mixed $inputData The input message to be hashed.
     * @param string|mixed $hashingKey The HMAC algorithm key.
     * @param bool|int|null $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return string The output HMAC digest.
     * @throws \Exception Validation errors.
     */
    public static function digest224($inputData, $hashingKey, $rawOutput = false)
    {
        return self::customHmac('sha3-224', $inputData, $hashingKey, $rawOutput);
    }

    /**
     * The HMAC-SHA-3-256 hashing function.
     *
     * @param string|mixed $inputData The input message to be hashed.
     * @param string|mixed $hashingKey The HMAC algorithm key.
     * @param bool|int|null $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return string The output HMAC digest.
     * @throws \Exception Validation errors.
     */
    public static function digest256($inputData, $hashingKey, $rawOutput = false)
    {
        return self::customHmac('sha3-256', $inputData, $hashingKey, $rawOutput);
    }

    /**
     * The HMAC-SHA-3-384 hashing function.
     *
     * @param string|mixed $inputData The input message to be hashed.
     * @param string|mixed $hashingKey The HMAC algorithm key.
     * @param bool|int|null $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return string The output HMAC digest.
     * @throws \Exception Validation errors.
     */
    public static function digest384($inputData, $hashingKey, $rawOutput = false)
    {
        return self::customHmac('sha3-384', $inputData, $hashingKey, $rawOutput);
    }

    /**
     * The HMAC-SHA-3-512 hashing function.
     *
     * @param string|mixed $inputData The input message to be hashed.
     * @param string|mixed $hashingKey The HMAC algorithm key.
     * @param bool|int|null $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return string The output HMAC digest.
     * @throws \Exception Validation errors.
     */
    public static function digest512($inputData, $hashingKey, $rawOutput = false)
    {
        return self::customHmac('sha3-512', $inputData, $hashingKey, $rawOutput);
    }
}
