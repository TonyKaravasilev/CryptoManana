<?php

/**
 * The RC4-128 pure PHP implementation that is compatible with newer PHP and OpenSLL versions.
 */

namespace CryptoManana\Compatibility;

use \CryptoManana\Core\Abstractions\DesignPatterns\AbstractSingleton as SingletonPattern;

/**
 * Class NativeRc4 - Pure PHP implementation of the RC4-128 algorithm.
 *
 * @package CryptoManana\Compatibility
 */
class NativeRc4 extends SingletonPattern
{
    /**
     * The internal secret key size measured in raw bytes length for the algorithm
     *
     * @internal For the current algorithm: 128 bits (16 bytes)
     */
    const KEY_SIZE = 16;

    /**
     * The key-scheduling algorithm for the initialize of the permutation array.
     *
     * @param string $key The secret key.
     *
     * @return array The key stream permutation array.
     */
    protected static function keyScheduling($key)
    {
        // The OpenSSL 128-bit key processing requirement
        $key = (strlen($key) < self::KEY_SIZE) ? str_pad($key, self::KEY_SIZE, "\x0", STR_PAD_RIGHT) : $key;

        $keyLength = strlen($key);
        $keyStream = [];

        for ($i = 0; $i < 256; $i++) {
            $keyStream[$i] = $i;
        }

        $j = 0;

        for ($i = 0; $i < 256; $i++) {
            // Note: mb_ord() is available only in PHP >= 7.2, so using ord() in ASCII 8-bit codes
            $j = ($j + $keyStream[$i] + ord($key[$i % $keyLength])) % 256;

            // Swapping positions
            $tmp = $keyStream[$i];
            $keyStream[$i] = $keyStream[$j];
            $keyStream[$j] = $tmp;
        }

        return $keyStream;
    }

    /**
     * The stream transformation operation realized via the internal pseudo-random generation algorithm.
     *
     * @param string $key The secret key.
     * @param string $data The data for transformation.
     *
     * @return string The encrypted or decrypted data.
     * @throws \Exception Validation errors.
     */
    protected static function transformData($key, $data)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException('The secret key parameter must be of type string.');
        } elseif (!is_string($data)) {
            throw new \InvalidArgumentException('The input data parameter must be of type string.');
        }

        $i = 0;
        $j = 0;
        $length = strlen($data);
        $keyStream = self::keyScheduling($key);

        for ($k = 0; $k < $length; $k++) {
            $i = ($i + 1) % 256;
            $ksi = $keyStream[$i];

            $j = ($j + $ksi) % 256;
            $ksj = $keyStream[$j];

            // Swapping positions
            $keyStream[$i] = $ksj;
            $keyStream[$j] = $ksi;

            // Note: mb_chr() and mb_ord() are available only in PHP >= 7.2, so using chr() and ord() in 8-bit codes
            $data[$k] = chr(ord($data[$k]) ^ $keyStream[($ksj + $ksi) % 256]);
        }

        return $data;
    }

    /**
     * Encrypts the given plain data.
     *
     * @param string $secretKey The secret key.
     * @param string $plainData The plain data for encryption.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     */
    public static function encryptData($secretKey, $plainData)
    {
        return self::transformData($secretKey, $plainData);
    }

    /**
     * Decrypts the given cipher data.
     *
     * @param string $secretKey The secret key.
     * @param string $cipherData The encrypted/cipher input string.
     *
     * @return string The decrypted/plain data.
     * @throws \Exception Validation errors.
     */
    public static function decryptData($secretKey, $cipherData)
    {
        return self::transformData($secretKey, $cipherData);
    }
}
