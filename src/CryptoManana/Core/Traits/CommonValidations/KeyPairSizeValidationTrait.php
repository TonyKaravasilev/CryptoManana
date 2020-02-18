<?php

/**
 * Trait implementation of asymmetric key pair size in bits validation methods.
 */

namespace CryptoManana\Core\Traits\CommonValidations;

/**
 * Trait KeyPairSizeValidationTrait - Reusable implementation of asymmetric key pair size validations.
 *
 * @package CryptoManana\Core\Traits\CommonValidations
 */
trait KeyPairSizeValidationTrait
{
    /**
     * Internal method for asymmetric algorithm type validation.
     *
     * @param int $keySize The key size in bits.
     *
     * @throws \Exception Validation errors.
     */
    protected function validateKeyPairSize($keySize)
    {
        $keySize = filter_var(
            $keySize,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 384,
                    "max_range" => 15360,
                ],
            ]
        );

        if ($keySize === false || $keySize % 128 !== 0) {
            throw new \InvalidArgumentException(
                'The key size must be between 384 (fastest but weakest) ' .
                'and 15360 (slowest but strongest) bits and be dividable by 128 bits.'
            );
        }
    }
}
