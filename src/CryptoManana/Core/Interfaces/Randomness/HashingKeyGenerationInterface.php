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
     * Generate a random HMAC key for hashing purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 128).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated HMAC key.
     */
    public function getHashingKey($length = 128, $printable = true);

    /**
     * Generate a random salt string for hashing purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 128).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated hashing salt.
     */
    public function getHashingSalt($length = 128, $printable = true);
}
