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
     * The symmetric secret key 128-bit size.
     */
    const SECRET_KEY_128_BITS = 16;

    /**
     * The symmetric secret key 192-bit size.
     */
    const SECRET_KEY_192_BITS = 24;

    /**
     * The symmetric secret key 256-bit size.
     */
    const SECRET_KEY_256_BITS = 32;

    /**
     * The symmetric initialization vector (IV) 128-bit size.
     */
    const IV_128_BITS = 16;

    /**
     * The symmetric initialization vector (IV) 192-bit size.
     */
    const IV_192_BITS = 24;

    /**
     * The symmetric initialization vector (IV) 256-bit size.
     */
    const IV_256_BITS = 32;

    /**
     * Generate a random encryption key for symmetrical cyphers.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 16).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated encryption key.
     */
    public function getEncryptionKey($length = self::SECRET_KEY_128_BITS, $printable = true);

    /**
     * Generate a random initialization vector (IV) for encryption purposes.
     *
     * Note: The output string can be in raw bytes of the `$printable` parameter is set to `true`.
     *
     * @param int $length The desired output length (default => 16).
     * @param bool|int $printable Flag for using only printable characters instead of bytes (default => true).
     *
     * @return string Randomly generated encryption initialization vector.
     * @throws \Exception Validation errors.
     */
    public function getEncryptionInitializationVector($length = self::IV_128_BITS, $printable = true);
}
