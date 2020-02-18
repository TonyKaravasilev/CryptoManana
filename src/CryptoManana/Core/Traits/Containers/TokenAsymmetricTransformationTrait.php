<?php

/**
 * Trait implementation for the generation and the transformation of authentication tokens.
 */

namespace CryptoManana\Core\Traits\Containers;

use CryptoManana\Core\Abstractions\Randomness\AbstractGenerator as RandomnessSource;
use CryptoManana\Core\Abstractions\MessageEncryption\AbstractAsymmetricEncryptionAlgorithm as AsymmetricEncryption;
use CryptoManana\Core\Interfaces\MessageEncryption\DataEncryptionInterface as DataEncryption;
use CryptoManana\Core\Interfaces\Containers\TokenTransformationInterface as TokenTransformationSpecification;
use CryptoManana\DataStructures\AuthenticationToken as AuthenticationTokenStructure;

/**
 * Trait TokenAsymmetricTransformationTrait - Reusable implementation of `TokenTransformationInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\Containers\TokenTransformationInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\Containers
 *
 * @property RandomnessSource $randomnessSource The randomness generator.
 * @property AsymmetricEncryption|DataEncryption|null $asymmetricCipherSource The message asymmetric encryption service.
 *
 * @mixin TokenTransformationSpecification
 */
trait TokenAsymmetricTransformationTrait
{
    /**
     * Generate a secure token and create an encrypt representation.
     *
     * @param int $length The desired output string length in bytes (default => 64).
     *
     * @return AuthenticationTokenStructure The authentication token data structure object.
     * @throws \Exception Validation errors.
     */
    public function generateAuthenticationToken($length = 64)
    {
        $length = filter_var(
            $length,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 1,
                    "max_range" => PHP_INT_MAX,
                ],
            ]
        );

        if ($length === false) {
            throw new \LengthException(
                'The internal length of the desired secure token must me at least 1 character long.'
            );
        }

        $token = $this->randomnessSource->getAlphaNumeric($length, true);

        return new AuthenticationTokenStructure($token, $this->asymmetricCipherSource->encryptData($token));
    }

    /**
     * Extracts the token from the cipher data via a predefined configuration.
     *
     * @param string $cipherToken The encrypted token string.
     *
     * @return string The decrypted authentication token.
     * @throws \Exception Validation errors.
     */
    public function extractAuthenticationToken($cipherToken)
    {
        return $this->asymmetricCipherSource->decryptData($cipherToken);
    }
}
