<?php

/**
 * Interface for specifying data padding capabilities and actions for asymmetric encryption algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface AsymmetricPaddingInterface - Interface for asymmetric padding capabilities and operations.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface AsymmetricPaddingInterface
{
    /**
     * The PKCS#1 (v1.5, RSA Cryptography Standard) padding representation.
     */
    const PKCS1_PADDING = OPENSSL_PKCS1_PADDING;

    /**
     * The OAEP (Optimal Asymmetric Encryption Padding) padding representation.
     */
    const OAEP_PADDING = OPENSSL_PKCS1_OAEP_PADDING;

    /**
     * Setter for the asymmetric data padding operation property.
     *
     * @param int $padding The padding standard integer code value.
     *
     * @throws \Exception Validation errors.
     */
    public function setPaddingStandard($padding);

    /**
     * Getter for the asymmetric data padding operation property.
     *
     * @return string The padding standard integer code value.
     */
    public function getPaddingStandard();

    /**
     * Getter for the minimum size of the padding bytes that are required/reserved by the algorithm.
     *
     * @return int The minimum reserved size of the padding bytes.
     */
    public function getPaddingReservedSize();

    /**
     * Enable long data processing via small chunks.
     *
     * @internal Using data chunks with asymmetric algorithms is discouraged.
     */
    public function enableChunkProcessing();

    /**
     * Disable long data processing via small chunks.
     *
     * @internal Using data chunks with asymmetric algorithms is discouraged.
     */
    public function disableChunkProcessing();
}
