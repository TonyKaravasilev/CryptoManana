<?php

/**
 * Trait implementation of the signature digestion capabilities and actions for asymmetric signature algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use \CryptoManana\Core\Interfaces\MessageEncryption\SignatureDigestionInterface as SignatureDigestionSpecification;

/**
 * Trait SignatureDigestionTrait - Reusable implementation of `SignatureDigestionInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\SignatureDigestionInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @property int $digestion The signature's internal digestion algorithm property.
 *
 * @mixin SignatureDigestionSpecification
 */
trait SignatureDigestionTrait
{
    /**
     * Setter for the signature's internal digestion algorithm property.
     *
     * @param int $signingAlgorithm The digestion algorithm integer code value.
     *
     * @return $this The signature algorithm object.
     * @throws \Exception Validation errors.
     */
    public function setSignatureDigestion($signingAlgorithm)
    {
        $validDigestions = [
            self::SHA1_SIGNING,
            self::SHA2_224_SIGNING,
            self::SHA2_256_SIGNING,
            self::SHA2_384_SIGNING,
            self::SHA2_512_SIGNING,
        ];

        $signingAlgorithm = filter_var(
            $signingAlgorithm,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 1,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($signingAlgorithm === false || !in_array($signingAlgorithm, $validDigestions, true)) {
            throw new \InvalidArgumentException(
                'The digestion algorithm standard must be a valid integer bigger than 0.'
            );
        }

        $this->digestion = $signingAlgorithm;

        return $this;
    }

    /**
     * Setter for the signature's internal digestion algorithm property.
     *
     * @return int The digestion algorithm integer code value.
     */
    public function getSignatureDigestion()
    {
        return $this->digestion;
    }
}
