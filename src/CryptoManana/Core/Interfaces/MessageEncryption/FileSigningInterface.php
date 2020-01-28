<?php

/**
 * Interface for specifying file signing/verifying for asymmetric signature algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface FileSigningInterface - Interface for file signing/verifying.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface FileSigningInterface
{
    /**
     * Generates a signature of a given plain file.
     *
     * @param string $filename The full path and name of the file for signing.
     *
     * @return string The signature data.
     * @throws \Exception Validation errors.
     */
    public function signFile($filename);

    /**
     * Verifies that the signature is correct for a given plain file.
     *
     * @param string $signatureData The signature input string.
     * @param string $filename The full path and name of the file for signing.
     *
     * @return bool The verification result.
     * @throws \Exception Validation errors.
     */
    public function verifyFileSignature($signatureData, $filename);
}
