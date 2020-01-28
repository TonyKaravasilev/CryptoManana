<?php

/**
 * Interface for specifying signature digestion capabilities and actions for asymmetric signature algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface SignatureDigestionInterface - Interface for signature digestion capabilities and operations.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface SignatureDigestionInterface
{
    /**
     * The SHA-1 algorithm signature digestion format.
     */
    const SHA1_SIGNING = OPENSSL_ALGO_SHA1;

    /**
     * The SHA-2-224 algorithm signature digestion format.
     */
    const SHA2_224_SIGNING = OPENSSL_ALGO_SHA224;

    /**
     * The SHA-2-256 algorithm signature digestion format.
     */
    const SHA2_256_SIGNING = OPENSSL_ALGO_SHA256;

    /**
     * The SHA-2-384 algorithm signature digestion format.
     */
    const SHA2_384_SIGNING = OPENSSL_ALGO_SHA384;

    /**
     * The SHA-2-512 algorithm signature digestion format.
     */
    const SHA2_512_SIGNING = OPENSSL_ALGO_SHA512;

    /**
     * Setter for the signature's internal digestion algorithm property.
     *
     * @param int $signingAlgorithm The digestion algorithm integer code value.
     *
     * @throws \Exception Validation errors.
     */
    public function setSignatureDigestion($signingAlgorithm);

    /**
     * Setter for the signature's internal digestion algorithm property.
     *
     * @return int The digestion algorithm integer code value.
     */
    public function getSignatureDigestion();
}
