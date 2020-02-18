<?php

/**
 * Trait implementation of the file signing/verifying for asymmetric signature algorithms.
 */

namespace CryptoManana\Core\Traits\MessageEncryption;

use CryptoManana\Core\Interfaces\MessageEncryption\FileSigningInterface as FileSigningSpecification;
use CryptoManana\Core\Interfaces\MessageEncryption\DataSigningInterface as DataSigningSpecification;
use CryptoManana\Core\Traits\CommonValidations\FileNameValidationTrait as ValidateFileNames;

/**
 * Trait FileSigningTrait - Reusable implementation of `FileSigningInterface`.
 *
 * @see \CryptoManana\Core\Interfaces\MessageDigestion\FileSigningInterface The abstract specification.
 *
 * @package CryptoManana\Core\Traits\MessageEncryption
 *
 * @mixin FileSigningSpecification
 * @mixin DataSigningSpecification
 * @mixin ValidateFileNames
 */
trait FileSigningTrait
{
    /**
     * File name and path validations.
     *
     * {@internal Reusable implementation of the common file name validation. }}
     */
    use ValidateFileNames;

    /**
     * Generates a signature of a given plain file.
     *
     * @param string $filename The full path and name of the file for signing.
     *
     * @return string The signature data.
     * @throws \Exception Validation errors.
     */
    public function signFile($filename)
    {
        if (!is_string($filename)) {
            throw new \InvalidArgumentException('The file path must be of type string.');
        }

        $this->validateFileNamePath($filename);

        return $this->signData(file_get_contents($filename));
    }

    /**
     * Verifies that the signature is correct for a given plain file.
     *
     * @param string $signatureData The signature input string.
     * @param string $filename The full path and name of the file for signing.
     *
     * @return bool The verification result.
     * @throws \Exception Validation errors.
     */
    public function verifyFileSignature($signatureData, $filename)
    {
        if (!is_string($signatureData)) {
            throw new \InvalidArgumentException('The signature data must be a string or a binary string.');
        } elseif (!is_string($filename)) {
            throw new \InvalidArgumentException('The file path must be of type string.');
        }

        $this->validateFileNamePath($filename);

        return $this->verifyDataSignature($signatureData, file_get_contents($filename));
    }
}
