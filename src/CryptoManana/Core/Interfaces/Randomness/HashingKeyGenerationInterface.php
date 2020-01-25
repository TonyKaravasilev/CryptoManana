<?php

/**
 * Interface for security hashing key and salt string generation capabilities.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface HashingKeyGenerationInterface -  Interface for hashing key and salt string generation.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface HashingKeyGenerationInterface
{
    /**
     * The hash digestion key 128-bit size.
     */
    const DIGESTION_KEY_128_BITS = 16;

    /**
     * The hash digestion key 160-bit size.
     */
    const DIGESTION_KEY_160_BITS = 20;

    /**
     * The hash digestion key 224-bit size.
     */
    const DIGESTION_KEY_224_BITS = 28;

    /**
     * The hash digestion key 256-bit size.
     */
    const DIGESTION_KEY_256_BITS = 32;

    /**
     * The hash digestion key 384-bit size.
     */
    const DIGESTION_KEY_384_BITS = 48;

    /**
     * The hash digestion key 512-bit size.
     */
    const DIGESTION_KEY_512_BITS = 64;

    /**
     * The hash digestion salt 128-bit size.
     */
    const DIGESTION_SALT_128_BITS = 16;

    /**
     * The hash digestion salt 160-bit size.
     */
    const DIGESTION_SALT_160_BITS = 20;

    /**
     * The hash digestion salt 224-bit size.
     */
    const DIGESTION_SALT_224_BITS = 28;

    /**
     * The hash digestion salt 256-bit size.
     */
    const DIGESTION_SALT_256_BITS = 32;

    /**
     * The hash digestion salt 384-bit size.
     */
    const DIGESTION_SALT_384_BITS = 48;

    /**
     * The hash digestion salt 512-bit size.
     */
    const DIGESTION_SALT_512_BITS = 64;

    /**
     * Generate a random HMAC key for hashing purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 16).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated HMAC key.
     */
    public function getHashingKey($length = self::DIGESTION_KEY_128_BITS, $printable = true);

    /**
     * Generate a random salt string for hashing purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 16).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated hashing salt.
     */
    public function getHashingSalt($length = self::DIGESTION_SALT_128_BITS, $printable = true);
}
