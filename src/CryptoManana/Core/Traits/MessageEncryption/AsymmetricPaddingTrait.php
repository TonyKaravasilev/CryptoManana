<?php

/**
 * Trait implementation of the data padding capabilities and actions for asymmetric encryption algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use \CryptoManana\Core\Interfaces\MessageEncryption\AsymmetricPaddingInterface as AsymmetricPaddingSpecification;

/**
 * Trait AsymmetricPaddingTrait - Reusable implementation of `AsymmetricPaddingInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\AsymmetricPaddingInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @property int $padding The asymmetric data padding operation property.
 * @property bool $useChunks Flag for enabling/disabling data processing via chunks.
 *
 * @mixin AsymmetricPaddingSpecification
 */
trait AsymmetricPaddingTrait
{
    /**
     * Setter for the asymmetric data padding operation property.
     *
     * @param int $padding The padding standard integer code value.
     *
     * @return $this The asymmetric encryption algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setPaddingStandard($padding)
    {
        $validPadding = [
            self::PKCS1_PADDING,
            self::OAEP_PADDING,
        ];

        $padding = filter_var(
            $padding,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 1,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($padding === false || !in_array($padding, $validPadding, true)) {
            throw new \InvalidArgumentException(
                'The digestion algorithm standard must be a valid integer bigger than 0.'
            );
        }

        $this->padding = $padding;

        return $this;
    }

    /**
     * Getter for the asymmetric data padding operation property.
     *
     * @return string The padding standard integer code value.
     */
    public function getPaddingStandard()
    {
        return $this->padding;
    }

    /**
     * Getter for the minimum size of the padding bytes that are required/reserved by the algorithm.
     *
     * @return int The minimum reserved size of the padding bytes.
     */
    public function getPaddingReservedSize()
    {
        /**
         * {@internal The reserved byte size for the PKCS1 standard is 11 and for the OAEP is 42. }}
         */
        return ($this->padding === OPENSSL_PKCS1_PADDING) ? 11 : 42;
    }

    /**
     * Enable long data processing via small chunks.
     *
     * @return $this The asymmetric encryption algorithm object.
     *
     * @internal Using data chunks with asymmetric algorithms is discouraged.
     */
    public function enableChunkProcessing()
    {
        $this->useChunks = true;

        return $this;
    }

    /**
     * Disable long data processing via small chunks.
     *
     * @return $this The asymmetric encryption algorithm object.
     * @internal Using data chunks with asymmetric algorithms is discouraged.
     */
    public function disableChunkProcessing()
    {
        $this->useChunks = false;


        return $this;
    }
}
