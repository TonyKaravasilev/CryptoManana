<?php

/**
 * Interface for security asymmetric key pair generation capabilities.
 */

namespace CryptoManana\Core\Interfaces\Containers;

use \CryptoManana\DataStructures\KeyPair as KeyPairStructure;

/**
 * Interface AsymmetricKeyPairGenerationInterface - Interface for asymmetric key pair generation.
 *
 * @package CryptoManana\Core\Interfaces\Containers
 */
interface AsymmetricKeyPairGenerationInterface
{
    /**
     * The RSA key pair type.
     */
    const RSA_KEY_PAIR_TYPE = OPENSSL_KEYTYPE_RSA;

    /**
     * The DSA/DSS key pair type.
     */
    const DSA_KEY_PAIR_TYPE = OPENSSL_KEYTYPE_DSA;

    /**
     * The asymmetric key pair 1024-bit size.
     */
    const KEY_PAIR_1024_BITS = 1024;

    /**
     * The asymmetric key pair 2048-bit size.
     */
    const KEY_PAIR_2048_BITS = 2048;

    /**
     * The asymmetric key pair 3072-bit size.
     */
    const KEY_PAIR_3072_BITS = 3072;

    /**
     * The asymmetric key pair 4096-bit size.
     */
    const KEY_PAIR_4096_BITS = 4096;

    /**
     * Generate a random key pair for asymmetrical cyphers.
     *
     * @param int $keySize The key size in bits.
     * @param int $algorithmType The asymmetric algorithm type integer code.
     *
     * @return KeyPairStructure Randomly generated asymmetric key pair (private and public keys) as an object.
     * @throws \Exception Validation errors.
     */
    public function getAsymmetricKeyPair($keySize = self::KEY_PAIR_4096_BITS, $algorithmType = self::RSA_KEY_PAIR_TYPE);
}
