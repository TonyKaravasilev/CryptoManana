<?php

/**
 * Interface for specifying object signing/verifying for asymmetric signature algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface ObjectSigningInterface - Interface for object signing/verifying.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface ObjectSigningInterface
{
    /**
     * Generates a signature of the given object.
     *
     * @param object|\stdClass $object The object for signing.
     *
     * @return string The signature data.
     * @throws \Exception Validation errors.
     */
    public function signObject($object);

    /**
     * Verifies that the signature is correct for the given object.
     *
     * @param string $signatureData The signature input string.
     * @param object|\stdClass $object The object used for signing.
     *
     * @return bool The verification result.
     * @throws \Exception Validation errors.
     */
    public function verifyObjectSignature($signatureData, $object);
}
