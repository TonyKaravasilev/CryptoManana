<?php

/**
 * Interface for specifying signature data output formats for signature algorithms.
 */

namespace CryptoManana\Core\Interfaces\MessageEncryption;

/**
 * Interface SignatureDataFormatsInterface - Interface for the signature output formats.
 *
 * @package CryptoManana\Core\Interfaces\MessageEncryption
 */
interface SignatureDataFormatsInterface
{
    /**
     * The signature output format for a raw byte string representation.
     *
     * @internal Output Format: `�7�f`
     */
    const SIGNATURE_OUTPUT_RAW = -1;

    /**
     * The signature output format for a upper case HEX string representation.
     *
     * @internal Output Format: `CF48`
     */
    const SIGNATURE_OUTPUT_HEX_UPPER = 0;

    /**
     * The signature output format for a lower case HEX string representation.
     *
     * @internal Output Format: `8d37`
     */
    const SIGNATURE_OUTPUT_HEX_LOWER = 1;

    /**
     * The signature output format for a Base64 standard string representation.
     *
     * @internal Output Format: `C7zGTA==`
     */
    const SIGNATURE_OUTPUT_BASE_64 = 2;

    /**
     * The signature output format for a Base64 URL friendly string representation.
     *
     * @internal Output Format: `C7zGTA`
     */
    const SIGNATURE_OUTPUT_BASE_64_URL = 3;

    /**
     * Setter for the output signature format code property.
     *
     * @param int $signatureFormat The output signature format code.
     *
     * @throws \Exception Validation errors.
     */
    public function setSignatureFormat($signatureFormat);

    /**
     * Getter for the output signature format code property.
     *
     * @return int The output signature format code.
     */
    public function getSignatureFormat();
}
