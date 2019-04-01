<?php

/**
 * Interface for security encryption key and initialization vector (IV) generation capabilities.
 */

namespace CryptoManana\Core\Interfaces\Randomness;

/**
 * Interface EncryptionKeyGenerationInterface - Interface for encryption key and IV generation.
 *
 * @package CryptoManana\Core\Interfaces\Randomness
 */
interface EncryptionKeyGenerationInterface
{
    /**
     * Generate a random encryption key for symmetrical cyphers.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 128).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated encryption key.
     */
    public function getEncryptionKey($length = 128, $printable = true);

    /**
     * Generate a random initialization vector (IV) for encryption purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 128).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated encryption initialization vector.
     * @throws \Exception Validation errors.
     */
    public function getEncryptionInitializationVector($length = 128, $printable = true);
}
