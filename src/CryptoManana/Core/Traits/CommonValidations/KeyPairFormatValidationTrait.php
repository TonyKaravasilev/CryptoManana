<?php

/**
 * Trait implementation of asymmetric key pair format validation methods.
 */

namespace CryptoManana\Core\Traits\CommonValidations;

/**
 * Trait KeyPairFormatValidationTrait - Reusable implementation of asymmetric key pair format validations.
 *
 * @package CryptoManana\Core\Traits\CommonValidations
 */
trait KeyPairFormatValidationTrait
{
    /**
     * Internal method for the validation of the private key string representation format.
     *
     * @param string $privateKey The private key input string.
     *
     * @throws \Exception Validation errors.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function validatePrivateKeyFormat(&$privateKey)
    {
        if (!is_string($privateKey)) {
            throw new \InvalidArgumentException('The private key must be a string or a binary string.');
        }

        $isPrivateBase64String = (
            !empty($privateKey) && preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $privateKey) && strlen($privateKey) % 4 === 0
        );

        if (!$isPrivateBase64String) {
            throw new \InvalidArgumentException('The private key must be a valid Base64 formatted string.');
        }
    }

    /**
     * Internal method for the validation of the public key string representation format.
     *
     * @param string $publicKey The public key input string.
     *
     * @throws \Exception Validation errors.
     *
     * @internal The parameter is passed via reference from the main logical method for performance reasons.
     */
    protected function validatePublicKeyFormat(&$publicKey)
    {
        if (!is_string($publicKey)) {
            throw new \InvalidArgumentException('The public key must be a string or a binary string.');
        }

        $isPublicBase64String = (
            !empty($publicKey) && preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $publicKey) && strlen($publicKey) % 4 === 0
        );

        if (!$isPublicBase64String) {
            throw new \InvalidArgumentException('The public key must be a valid Base64 formatted string.');
        }
    }
}
