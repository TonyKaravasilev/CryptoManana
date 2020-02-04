<?php

/**
 * Interface for specifying data signing/verifying for asymmetric signature algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface DataSigningInterface - Interface for data signing/verifying.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface DataSigningInterface
{
    /**
     * Encrypts the given plain data.
     *
     * @param string $plainData The plain input string.
     *
     * @return string The cipher/encrypted data.
     * @throws \Exception Validation errors.
     */

    /**
     * Generates a signature of the given plain data.
     *
     * @param string $plainData The plain input string.
     *
     * @return string The signature data.
     * @throws \Exception Validation errors.
     */
    public function signData($plainData);

    /**
     * Verifies that the signature is correct for the given plain data.
     *
     * @param string $signatureData The signature input string.
     * @param string $plainData The plain input string.
     *
     * @return bool The verification result.
     * @throws \Exception Validation errors.
     */
    public function verifyDataSignature($signatureData, $plainData);
}
